<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Handler;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Weblabel\WorkerBundle\Bus\MessageBusAwareInterface;

abstract class AbstractHandler implements MessageHandlerInterface, MessageBusAwareInterface
{
    private MessageBusInterface $messageBus;

    public function dispatch(object $message, array $stamps = []): Envelope
    {
        return $this->getMessageBus()->dispatch($message, $stamps);
    }

    public function setMessageBus(MessageBusInterface $messageBus): void
    {
        $this->messageBus = $messageBus;
    }

    public function getMessageBus(): MessageBusInterface
    {
        return $this->messageBus;
    }
}
