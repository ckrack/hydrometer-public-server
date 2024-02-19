<?php

namespace App\Console;

use App\Command\AddDataCommand;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Ulid;

#[AsCommand(
    name: 'tcp-server:start',
    description: 'Start the TCP server used to receive data from ISpindel.',
)]
class TcpServerStartConsoleCommand extends Command
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'port',
                'p',
                InputOption::VALUE_REQUIRED,
                'Port to use',
                (int) $_ENV['TCP_API_PORT']);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Set time limit to indefinite execution
        set_time_limit(0);

        // open TCP server
        $server = stream_socket_server('tcp://0.0.0.0:'.$input->getOption('port'), $errno, $errorMessage);

        if (!$server) {
            $this->logger->error('Could not bind to socket: '.$errorMessage);
            throw new \RuntimeException(sprintf('Could not bind to socket: %s', $errorMessage));
        }

        $io = new SymfonyStyle($input, $output);
        $io->success('Socket server open. Listening on: '.$input->getOption('port'));
        $this->logger->info('socket server open', [$input->getOption('port')]);

        while (true) { // @phpstan-ignore-line
            try {
                $jsonData = $this->handleClients($server);
                if (is_array($jsonData)) {
                    $this->dispatchAddDataCommand($jsonData);
                }
            } catch (\Throwable $exception) {
                $this->logger->error('Exception while handling clients: '.$exception->getMessage().$exception->getFile().$exception->getLine());
            }
        }
    }

    /**
     * @param array<array-key, mixed> $jsonData
     *
     * @throws \JsonException
     */
    private function dispatchAddDataCommand(array $jsonData): void
    {
        if (!array_key_exists('token', $jsonData)) {
            throw new \InvalidArgumentException('No token in data');
        }

        $this->messageBus->dispatch(new AddDataCommand(new Ulid($jsonData['token']), json_encode($jsonData, JSON_THROW_ON_ERROR)));
    }

    /**
     * @param false|resource $server
     *
     * @return array<array-key, mixed>|null
     *
     * @throws \JsonException
     */
    private function handleClients($server): ?array
    {
        try {
            if (!is_resource($server)) {
                return null;
            }
            $client = @stream_socket_accept($server, 5);
            if ($client) {
                $this->logger->info('client connected');
                $jsonRaw = $this->readJsonFromClient($client);
                // close connection to client
                stream_socket_shutdown($client, STREAM_SHUT_RDWR);
                fclose($client);
                $this->logger->info('Closed client');

                $jsonDecoded = json_decode($jsonRaw, true, 512, JSON_THROW_ON_ERROR);

                if (!\is_array($jsonDecoded)) {
                    throw new \InvalidArgumentException('Invalid JSON received');
                }

                $this->logger->debug('Spindle data', [$jsonDecoded]);

                return $jsonDecoded;
            }
        } catch (\Exception $exception) {
            throw $exception;
        }

        return null;
    }

    /**
     * @param resource $client
     */
    private function readJsonFromClient($client): string
    {
        // read from input until blank line
        $jsonRaw = '';
        while ($tcpInput = fread($client, 1024)) {
            $this->logger->info('Input: '.$tcpInput);
            $jsonRaw .= $tcpInput;

            // get input until json closing bracket
            if (str_contains($tcpInput, '}')) {
                $this->logger->info('Json received.');
                break;
            }
        }

        return $jsonRaw;
    }
}
