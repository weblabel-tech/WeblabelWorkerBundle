<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Tests\Unit\Handler\Middleware;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Weblabel\WorkerBundle\Handler\AbstractHandler;
use Weblabel\WorkerBundle\Handler\Middleware\ReplyStampMiddleware;
use Weblabel\WorkerBundle\Stamp\ReplyToStamp;
use Weblabel\WorkerBundle\Stamp\StampsAwareInterface;

class ReplyStampMiddlewareTest extends TestCase
{
    public function test_injecting_reply_to_stamp()
    {
        $messageBus = $this->createMock([MessageBusInterface::class, StampsAwareInterface::class]);
        $messageBus
            ->expects(self::once())
            ->method('addStamp')
            ->with(new AmqpStamp('foo'));

        $handler = new class() extends AbstractHandler {
            public function __invoke()
            {
            }
        };
        $handler->setMessageBus($messageBus);

        $command = new \stdClass();
        $replyToStamp = new ReplyToStamp('foo');
        $envelope = new Envelope($command, [$replyToStamp]);

        $middleware = new ReplyStampMiddleware();
        $middleware->process($handler, $envelope);
    }
}
