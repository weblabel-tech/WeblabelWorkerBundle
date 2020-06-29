<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Middleware;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Weblabel\WorkerBundle\Stamp\HandlerIdStamp;

final class AddHandlerIdMiddleware implements MiddlewareInterface
{
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $envelope = $envelope->with(HandlerIdStamp::create($this->getParentHandlerId($envelope)));

        return $stack->next()->handle($envelope, $stack);
    }

    private function getParentHandlerId(Envelope $envelope): ?string
    {
        /** @var HandlerIdStamp|null $previousHandlerIdStamp */
        $previousHandlerIdStamp = $envelope->last(HandlerIdStamp::class);
        if (null === $previousHandlerIdStamp) {
            return null;
        }

        return $previousHandlerIdStamp->getHandlerId();
    }
}
