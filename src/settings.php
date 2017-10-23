<?php

// load .env
if (file_exists(__DIR__.'/.env')) {
    $dotenv = new Dotenv\Dotenv(__DIR__);
    $dotenv->load();
}

// define for use in swagger annotations
if (!defined('API_HOST')) {
    define('API_HOST', getenv('API_HOST'));
}
if (!defined('API_PROTOCOL')) {
    define('API_PROTOCOL', getenv('API_PROTOCOL'));
}

date_default_timezone_set("Europe/Berlin");

return [
    'settings' => [
        'displayErrorDetails' => getenv('DISPLAY_ERRORS'), // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => getenv('LOG_FILE'),
        ],

        // Plates settings
        'view' => [
            // Path to view directory (default: null)
            'directory' => __DIR__."/templates",
            // Path to asset directory (default: null)
            'assetPath' => __DIR__.'/../public',
            // Template extension (default: 'php')
            'fileExtension' => null,
            // Template extension (default: false) see: http://platesphp.com/extensions/asset/
            'timestampInFilename' => false,
        ],

        'hashids' => [
            'salt' => getenv('HASHIDS_SALT'),
            'minlength' => getenv('HASHIDS_MINLENGTH')
        ],

        'optimus' => [
            'prime' => getenv('OPTIMUS_PRIME'),
            'inverse' => getenv('OPTIMUS_INVERSE'),
            'random' => getenv('OPTIMUS_RANDOM'),
        ],

        'smtp' => [
            'server' => getenv('SMTP_SERVER'),
            'port' => getenv('SMTP_PORT'),
            'username' => getenv('SMTP_USER'),
            'password' => getenv('SMTP_PASS'),
        ],

        'doctrine' => [
            'meta' => [
                'entity_path' => ['src/Entity'],
                'proxy_dir' =>  __DIR__.'/Proxies',
                'proxy_namespace' => 'App\Proxies',
            ],
            'connection' => [
                'driver'   => getenv('DB_DRIVER'),
                'host'     => getenv('DB_HOST'),
                'dbname'   => getenv('DB_DATABASE'),
                'user'     => getenv('DB_USER'),
                'password' => getenv('DB_PASS'),
                'charset'  => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'port' => getenv('DB_PORT'),
            ],
        ],
        'languages' => [
            'path'  => realpath(getenv('LANGUAGE_PATH')),
            'list' => [
                'en' => [
                    // the name in it's language
                    'name' => 'English',
                    // a list of valid locales
                    'locales' => ['en_EN', 'en_GB', 'en_GB.UTF8', 'en_US', 'en_US.UTF8', 'English_Australia.1252']
                    // it is a good idea to symlink all locales to the main one.
                ],
                'de' => [
                    'name' => 'Deutsch',
                    'locales' => ['de_DE', 'de_DE.UTF8', 'German_Germany.1252', 'de_CH', 'de_AT']
                ],
            ]
        ],
    ],
];
