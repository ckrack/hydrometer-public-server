#!/usr/local/bin/php
<?php
/*
 * TCP Stream socket server
 */
use League\Container\Container;

require_once '../vendor/autoload.php';

$settings = require __DIR__ . '/../src/settings.php';
$container = new Container;
require __DIR__ . '/../src/dependencies.php';

// Set time limit to indefinite execution
set_time_limit (0);

$logger = $container->get('Psr\Log\LoggerInterface');
$em = $container->get('Doctrine\ORM\EntityManager');
$TCP = $container->get('App\Modules\Ispindle\TCP');

// open TCP server
$server = stream_socket_server("tcp://".getenv('TCP_API_HOST').":".getenv('TCP_API_PORT'), $errno, $errorMessage);

if ($server === false) {
    $logger->error('Could not bind to socket: ' . $errorMessage);
    throw new UnexpectedValueException("Could not bind to socket: $errorMessage");
}

echo "socket server open\n";
$logger->info("socket server open");

while (true) {
    $client = @stream_socket_accept($server);

    if ($client) {
        try {
            $logger->info("client connected");

            // read three lines from input
            $input = fread($client, 1024);
            $input .= fread($client, 1024);
            $input .= fread($client, 1024);

            $logger->info('Input: ' . $input);

            // close connection to client
            stream_socket_shutdown($client, STREAM_SHUT_RDWR);

            $logger->info('Closed client');

            fclose($client);

            // now handle data

            if (! $TCP->validateInput($input)) {
                $logger->error('Invalid input', [$input]);
            }

            $json = json_decode($input, true);

            if ((!is_array($json) && !is_object($json)) || json_last_error()) {
                // data not ok

                $logger->info('Spindle data not ok', [$json, json_last_error()]);

                continue;
            }

            $logger->debug('Spindle data', [$json]);

            // confirm existance of the token
            $authData = $TCP->authenticate($json['token']);
            $TCP->saveData($json, $authData['hydrometer_id'], $authData['fermentation_id']);

        } catch (\Exception $e) {
            $logger->error('Exception: ' . $e->getMessage());
            throw $e;
        }
    }
}

