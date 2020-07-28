<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Command;

use App\Modules\Auth\Token;
use App\Modules\Ispindle\TCP;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class IspindelTcpServerCommand extends Command
{
    protected static $defaultName = 'app:ispindel-tcp-server';
    private TCP $tcp;
    private Token $tokenAuth;
    private LoggerInterface $logger;

    public function __construct(
        TCP $tcp,
        Token $tokenAuth,
        LoggerInterface $logger
    ) {
        $this->tcp = $tcp;
        $this->tokenAuth = $tokenAuth;
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

                        // get input until json closing bracket
                        if (mb_strpos(trim($tcpInput), '}') == !false) {
                            $this->logger->info('Breaking');
                            break;
                        }
                    }

                    $this->logger->info('Compiled input: '.$jsonRaw);

                    // now handle data
                    if (!$this->tcp->validateInput($jsonRaw)) {
                        $this->logger->error('Invalid input', [$jsonRaw]);
                        continue;
                    }

                    $jsonDecoded = json_decode($jsonRaw, true, 512, JSON_THROW_ON_ERROR);

                    if ((!\is_array($jsonDecoded) && !\is_object($jsonDecoded)) || json_last_error()) {
                        $this->logger->info('Spindle data not ok', [$jsonDecoded, json_last_error()]);
                        continue;
                    }

                    $this->logger->debug('Spindle data', [$jsonDecoded]);

                    // wake the database connection
                    $this->tcp->wakeupDb();

                    // confirm existance of the token @throws
                    $authData = $this->tokenAuth->authenticate($jsonDecoded['token']);
                    $this->tcp->saveData($jsonDecoded, $authData['hydrometer_id'], $authData['fermentation_id']);

                    // write new or current interval
                    fwrite($client, json_encode((object) ['interval' => $authData['interval'] ?? $jsonDecoded['interval']], JSON_THROW_ON_ERROR));

                    // close connection to client
                    stream_socket_shutdown($client, STREAM_SHUT_RDWR);
                    fclose($client);
                    $this->logger->info('Closed client');

                    // sleep the database connection
                    $this->tcp->sleepDb();
                } catch (\Exception $e) {
                    $this->logger->error('Exception: '.$e->getMessage().$e->getFile().$e->getLine());
                }
                $this->logger->info('server done', [$input->getOption('port')]);
            }
        }

        return 0;
    }
}
