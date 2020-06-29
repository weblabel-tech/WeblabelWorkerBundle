<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Tests\Unit\Handler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\StampInterface;
use Weblabel\WorkerBundle\Handler\AbstractHandler;

class AbstractHandlerTest extends TestCase
{
    public function test_dispatching_message()
    {
        $messageBus = $this->createMock(MessageBusInterface::class);

        $command = new \stdClass();
        $stamps = [$this->createMock(StampInterface::class)];

        $envelope = new Envelope($command);
        $messageBus
            ->expects(self::once())
            ->method('dispatch')
            ->with($command, $stamps)
            ->willReturn($envelope);

        $handler = new class() extends AbstractHandler {
        };
        $handler->setMessageBus($messageBus);
        $handler->dispatch($command, $stamps);
    }
}
