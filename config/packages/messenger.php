<?php

use App\Event\HydrometerAddedEvent;
use App\Event\HydrometerDataReceivedEvent;
use App\Messenger\Middleware\PersistEventMiddleware;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->messenger() // @phpstan-ignore-line
        ->transport('sync')
        ->dsn('sync://');

    $framework->messenger()
        ->bus('messenger.bus.default')
        ->middleware()->id(PersistEventMiddleware::class);

    $framework->messenger() // @phpstan-ignore-line
        ->routing(HydrometerAddedEvent::class)
        ->senders(['sync']);

    $framework->messenger() // @phpstan-ignore-line
        ->routing(HydrometerDataReceivedEvent::class)
        ->senders(['sync']);
};
