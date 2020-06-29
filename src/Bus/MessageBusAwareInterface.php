<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Bus;

use Symfony\Component\Messenger\MessageBusInterface;

interface MessageBusAwareInterface
{
    public function setMessageBus(MessageBusInterface $messageBus): void;

    public function getMessageBus(): MessageBusInterface;
}
