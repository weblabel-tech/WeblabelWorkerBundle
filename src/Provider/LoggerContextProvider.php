<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Provider;

use Symfony\Component\Messenger\Envelope;
use Weblabel\WorkerBundle\Stamp\ExecutionStartTimeStamp;
use Weblabel\WorkerBundle\Stamp\HandlerIdStamp;

class LoggerContextProvider
{
    public function getGlobalContext(Envelope $envelope): array
    {
        return [
            'handlerId' => $this->getHandlerId($envelope),
            'command' => \get_class($envelope->getMessage()),
        ];
    }

    public function getHandlerContext(Envelope $envelope): array
    {
        return [
            'executionTime' => $this->getExecutionTime($envelope),
        ];
    }

    private function getHandlerId(Envelope $envelope): ?string
    {
        /** @var HandlerIdStamp|null $handlerIdStamp */
        $handlerIdStamp = $envelope->last(HandlerIdStamp::class);
        if (null === $handlerIdStamp) {
            return null;
        }

        return $handlerIdStamp->getHandlerId();
    }

    private function getExecutionTime(Envelope $envelope): ?float
    {
        /** @var ExecutionStartTimeStamp|null $executionStartTime */
        $executionStartTime = $envelope->last(ExecutionStartTimeStamp::class);
        if (null === $executionStartTime) {
            return null;
        }

        return \microtime(true) - $executionStartTime->getStartTime();
    }
}
