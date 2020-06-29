<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Middleware;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\SentStamp;
use Weblabel\WorkerBundle\Logger\ContextAwareLogger;
use Weblabel\WorkerBundle\Stamp\HandlerIdStamp;

final class SenderAuditMiddleware implements MiddlewareInterface
{
    private ContextAwareLogger $logger;

    public function __construct(ContextAwareLogger $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        if (null === $envelope->last(HandlerIdStamp::class)) {
            return $stack->next()->handle($envelope, $stack);
        }

        $envelope = $stack->next()->handle($envelope, $stack);
        if ($envelope->last(SentStamp::class)) {
            $this->logger->info('Command sent');
        }

        return $envelope;
    }
}
