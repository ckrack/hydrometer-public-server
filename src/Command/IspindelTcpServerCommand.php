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
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Set time limit to indefinite execution
        set_time_limit(0);

        // open TCP server
        //$server = stream_socket_server('tcp://'.getenv('TCP_API_HOST').':'.getenv('TCP_API_PORT'), $errno, $errorMessage);
        $server = stream_socket_server('tcp://0.0.0.0:'.getenv('TCP_API_PORT'), $errno, $errorMessage);

        if (false === $server) {
            $this->logger->error('Could not bind to socket: '.$errorMessage);
            throw new \UnexpectedValueException("Could not bind to socket: $errorMessage");
        }

        $io = new SymfonyStyle($input, $output);
        $io->success('Socket server open.');
        $io->success('listening on: '.getenv('TCP_API_HOST').':'.getenv('TCP_API_PORT'));

        $this->logger->info('socket server open', [getenv('TCP_API_HOST'), getenv('TCP_API_PORT')]);

        while (true) {
            $client = @stream_socket_accept($server, 5);
            $this->logger->info('client appeared?', [$client]);

            if ($client) {
                try {
                    $this->logger->info('client connected');

                    // read from input until blank line
                    $jsonRaw = '';
                    while ($input = fread($client, 1024)) {
                        $this->logger->info('Input: '.$input);
                        $this->logger->info('Empty?', ['' === $input]);
                        $jsonRaw .= $input;

                        if ('' === trim($input)) {
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
                $this->logger->info('server done', [getenv('TCP_API_HOST'), getenv('TCP_API_PORT')]);
            }
        }

        return 0;
    }
}
