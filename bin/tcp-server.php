#!/usr/local/bin/php
<?php
/*
 * TCP Stream socket server
 */
use League\Container\Container;

require __DIR__.'/../vendor/autoload.php';

$settings = require __DIR__.'/../src/settings.php';
$container = new Container();
require __DIR__.'/../src/dependencies.php';

// Set time limit to indefinite execution
set_time_limit(0);

$logger = $container->get('Psr\Log\LoggerInterface');
$em = $container->get('Doctrine\ORM\EntityManager');
$TCP = $container->get('App\Modules\Ispindle\TCP');

// open TCP server
$server = stream_socket_server('tcp://'.getenv('TCP_API_HOST').':'.getenv('TCP_API_PORT'), $errno, $errorMessage);

if (false === $server) {
    $logger->error('Could not bind to socket: '.$errorMessage);
    throw new UnexpectedValueException("Could not bind to socket: $errorMessage");
}

echo "socket server open\n";
echo 'listening on: '.getenv('TCP_API_HOST').':'.getenv('TCP_API_PORT')."\n";
$logger->info('socket server open', [getenv('TCP_API_HOST'), getenv('TCP_API_PORT')]);

while (true) {
    $client = @stream_socket_accept($server, 5);

    if ($client) {
        try {
            $logger->info('client connected');

            // read from input until blank line
            $jsonRaw = '';
            while ($input = fread($client, 1024)) {
                $logger->info('Input: '.$input);
                $logger->info('Empty?', ['' === $input]);
                $jsonRaw .= $input;

                if ('' === trim($input)) {
                    break;
                }
            }

            $logger->info('Input: '.$jsonRaw);

            // close connection to client
            stream_socket_shutdown($client, STREAM_SHUT_RDWR);

            $logger->info('Closed client');

            fclose($client);

            // now handle data
            if (!$TCP->validateInput($jsonRaw)) {
                $logger->error('Invalid input', [$jsonRaw]);
            }

            $jsonDecoded = json_decode($jsonRaw, true);

            if ((!is_array($jsonDecoded) && !is_object($jsonDecoded)) || json_last_error()) {
                // data not ok

                $logger->info('Spindle data not ok', [$jsonDecoded, json_last_error()]);

                continue;
            }

            $logger->debug('Spindle data', [$jsonDecoded]);

            // confirm existance of the token @throws
            $authData = $TCP->authenticate($jsonDecoded['token']);
            $TCP->saveData($jsonDecoded, $authData['hydrometer_id'], $authData['fermentation_id']);
        } catch (\Exception $e) {
            $logger->error('Exception: '.$e->getMessage());
            throw $e;
        }
    }
}
