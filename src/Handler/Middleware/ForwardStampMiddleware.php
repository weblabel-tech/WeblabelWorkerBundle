<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Handler\Middleware;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Weblabel\WorkerBundle\Bus\MessageBusAwareInterface;
use Weblabel\WorkerBundle\Handler\MiddlewareInterface;
use Weblabel\WorkerBundle\Stamp\ForwardableStampInterface;
use Weblabel\WorkerBundle\Stamp\StampsAwareInterface;

final class ForwardStampMiddleware implements MiddlewareInterface
{
    public function process(callable $handler, Envelope $envelope): void
    {
        if ($handler instanceof MessageBusAwareInterface) {
            $forwardableStamps = $this->getForwardableStamps($envelope);
            $this->addStamps($handler, $forwardableStamps);
        }
    }

    private function addStamps(MessageBusAwareInterface $handler, array $stamps): void
    {
        /** @var MessageBusInterface|StampsAwareInterface $messageBus */
        $messageBus = $handler->getMessageBus();
        if (!$messageBus instanceof StampsAwareInterface) {
            return;
        }

        foreach ($stamps as $stamp) {
            $messageBus->addStamp($stamp);
        }
    }

    private function getForwardableStamps(Envelope $envelope): array
    {
        $forwardableStamps = [[]];
        foreach ($envelope->all() as $stamps) {
            if (!\current($stamps) instanceof ForwardableStampInterface) {
                continue;
            }

            $forwardableStamps[] = $stamps;
        }

        return \array_merge(...$forwardableStamps);
    }
}
