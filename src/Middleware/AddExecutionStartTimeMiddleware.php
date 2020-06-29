<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Middleware;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Weblabel\WorkerBundle\Stamp\ExecutionStartTimeStamp;

final class AddExecutionStartTimeMiddleware implements MiddlewareInterface
{
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $envelope = $envelope->with(new ExecutionStartTimeStamp(\microtime(true)));

        return $stack->next()->handle($envelope, $stack);
    }
}
