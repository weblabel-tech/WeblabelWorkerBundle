<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Handler\Middleware;

use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Envelope;
use Weblabel\WorkerBundle\Bus\MessageBusAwareInterface;
use Weblabel\WorkerBundle\Handler\MiddlewareInterface;
use Weblabel\WorkerBundle\Stamp\ReplyToStamp;
use Weblabel\WorkerBundle\Stamp\StampsAwareInterface;

final class ReplyStampMiddleware implements MiddlewareInterface
{
    public function process(callable $handler, Envelope $envelope): void
    {
        if (!$handler instanceof MessageBusAwareInterface) {
            return;
        }

        $messageBus = $handler->getMessageBus();
        if (!$messageBus instanceof StampsAwareInterface) {
            return;
        }

        /** @var ReplyToStamp $replyToStamp */
        $replyToStamp = $envelope->last(ReplyToStamp::class);
        if (null === $replyToStamp) {
            return;
        }

        $messageBus->addStamp(new AmqpStamp($replyToStamp->getReplyTo()));
    }
}
