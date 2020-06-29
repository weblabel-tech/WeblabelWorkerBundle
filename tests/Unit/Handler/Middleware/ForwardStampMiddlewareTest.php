<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Tests\Unit\Handler\Middleware;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\StampInterface;
use Weblabel\WorkerBundle\Handler\AbstractHandler;
use Weblabel\WorkerBundle\Handler\Middleware\ForwardStampMiddleware;
use Weblabel\WorkerBundle\Stamp\ForwardableStampInterface;
use Weblabel\WorkerBundle\Stamp\StampsAwareInterface;

class ForwardStampMiddlewareTest extends TestCase
{
    public function test_injecting_forwardable_stamps()
    {
        $firstForwardableStamp = $this->createMock(ForwardableStampInterface::class);
        $secondForwardableStamp = $this->createMock(ForwardableStampInterface::class);
        $defaultStamp = $this->createMock(StampInterface::class);

        $messageBus = $this->createMock([MessageBusInterface::class, StampsAwareInterface::class]);
        $messageBus
            ->expects(self::exactly(2))
            ->method('addStamp')
            ->withConsecutive(
                [$firstForwardableStamp],
                [$secondForwardableStamp],
            );

        $handler = new class() extends AbstractHandler {
            public function __invoke()
            {
            }
        };
        $handler->setMessageBus($messageBus);

        $command = new \stdClass();
        $envelope = new Envelope($command, [$defaultStamp, $firstForwardableStamp, $secondForwardableStamp]);

        $middleware = new ForwardStampMiddleware();
        $middleware->process($handler, $envelope);
    }
}
