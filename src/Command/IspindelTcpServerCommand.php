<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Command;

use App\Modules\Ispindle\TCP;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class IspindelTcpServerCommand extends Command
{
    protected static $defaultName = 'app:ispindel-tcp-server';
    protected TCP $tcp;
    protected LoggerInterface $logger;

    public function __construct(
        TCP $tcp,
        LoggerInterface $logger
    ) {
        $this->tcp = $tcp;
        $this->logger = $logger;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Start the TCP server to listen for ispindel input')
            ->addOption('port', 'p', InputOption::VALUE_REQUIRED, 'Port to use')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Set time limit to indefinite execution
        set_time_limit(0);

        // open TCP server
        $server = stream_socket_server('tcp://0.0.0.0:'.$input->getOption('port'), $errno, $errorMessage);

        if (false === $server) {
            $this->logger->error('Could not bind to socket: '.$errorMessage);
            throw new \UnexpectedValueException("Could not bind to socket: $errorMessage");
        }

        $io = new SymfonyStyle($input, $output);
        $io->success('Socket server open.');
        $io->success('listening on: '.$input->getOption('port'));

        $this->logger->info('socket server open', [$input->getOption('port')]);

        while (true) {
            $client = @stream_socket_accept($server, 5);
            $this->logger->info('client appeared?', [$client]);

            if ($client) {
                try {
                    $this->logger->info('client connected');

                    // read from input until blank line
                    $jsonRaw = '';
                    while ($tcpInput = fread($client, 1024)) {
                        $this->logger->info('Input: '.$tcpInput);
                        $this->logger->info('Empty?', ['' === $tcpInput]);
                        $jsonRaw .= $tcpInput;

                        if ('' === trim($tcpInput)) {
                            break;
                        }
                    }

                    $this->logger->info('Compiled input: '.$jsonRaw);

                    // close connection to client
                    stream_socket_shutdown($client, STREAM_SHUT_RDWR);

                    $this->logger->info('Closed client');

                    fclose($client);

                    // now handle data
                    if (!$this->tcp->validateInput($jsonRaw)) {
                        $this->logger->error('Invalid input', [$jsonRaw]);
                    }

                    $jsonDecoded = json_decode($jsonRaw, true);

                    if ((!is_array($jsonDecoded) && !is_object($jsonDecoded)) || json_last_error()) {
                        // data not ok
                        $this->logger->info('Spindle data not ok', [$jsonDecoded, json_last_error()]);

                        continue;
                    }

                    $this->logger->debug('Spindle data', [$jsonDecoded]);

                    // wake the database connection
                    $this->tcp->wakeupDb();

                    // confirm existance of the token @throws
                    $authData = $this->tcp->authenticate($jsonDecoded['token']);
                    $this->tcp->saveData($jsonDecoded, $authData['hydrometer_id'], $authData['fermentation_id']);

                    // sleep the database connection
                    $this->tcp->sleepDb();
                } catch (\Exception $e) {
                    $this->logger->error('Exception: '.$e->getMessage());
                }
                $this->logger->info('server done', [$input->getOption('port')]);
            }
        }

        return 0;
    }
}
