<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Middleware;

use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
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

        $context = [];
        $replyTo = $this->getReplyTo($envelope);
        if (null !== $replyTo) {
            $context['replyTo'] = $replyTo;
        }

        $envelope = $stack->next()->handle($envelope, $stack);
        if ($envelope->last(SentStamp::class)) {
            $this->logger->info('Command sent', $context);
        }

        return $envelope;
    }

    private function getReplyTo(Envelope $envelope): ?string
    {
        /** @var AmqpStamp $amqpStamp */
        $amqpStamp = $envelope->last(AmqpStamp::class);
        if (null === $amqpStamp) {
            return null;
        }

        return $amqpStamp->getRoutingKey();
    }
}
