<?php

namespace App\Messenger\Middleware;

use App\Entity\Event;
use App\Event\EventInterface;
use App\Messenger\Stamp\EventPersistedStamp;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class PersistEventMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();
        if ($message instanceof EventInterface && !$envelope->all(EventPersistedStamp::class)) {
            try {
                $eventEntity = Event::fromEvent($message);
                $this->entityManager->persist($eventEntity);
                $this->entityManager->flush();

                return $stack->next()->handle($envelope->with(new EventPersistedStamp()), $stack);
            } catch (\Exception $e) {
                $this->logger->error('Could not persist event.'.$e->getMessage(), [$message]);
            }
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
