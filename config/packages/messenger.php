<?php

use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->messenger() // @phpstan-ignore-line
        ->transport('sync')
        ->dsn('sync://');

    $framework->messenger()
        ->bus('messenger.bus.default')
        ->middleware()->id(App\Messenger\Middleware\PersistEventMiddleware::class);

    $framework->messenger() // @phpstan-ignore-line
        ->routing(App\Event\HydrometerAddedEvent::class)
        ->senders(['sync']);

    $framework->messenger() // @phpstan-ignore-line
        ->routing(App\Event\HydrometerDataReceivedEvent::class)
        ->senders(['sync']);
};
