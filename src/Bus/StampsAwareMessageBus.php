<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Bus;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\StampInterface;
use Weblabel\WorkerBundle\Stamp\StampsAwareInterface;

final class StampsAwareMessageBus implements MessageBusInterface, StampsAwareInterface
{
    private MessageBusInterface $messageBus;

    private array $stamps;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
        $this->stamps = [];
    }

    public function dispatch($message, array $stamps = []): Envelope
    {
        $stamps = \array_merge($stamps, $this->stamps);

        return $this->messageBus->dispatch($message, $stamps);
    }

    public function addStamp(StampInterface $stamp): void
    {
        $this->stamps[] = $stamp;
    }
}
