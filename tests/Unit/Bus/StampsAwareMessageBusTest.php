<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Tests\Unit\Bus;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\StampInterface;
use Weblabel\WorkerBundle\Bus\StampsAwareMessageBus;

class StampsAwareMessageBusTest extends TestCase
{
    public function test_dispatching_message_with_injected_stamps()
    {
        $command = new \stdClass();
        $firstStamp = $this->createMock(StampInterface::class);
        $secondStamp = $this->createMock(StampInterface::class);
        $envelope = new Envelope($command, [$firstStamp, $secondStamp]);

        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus
            ->expects(self::once())
            ->method('dispatch')
            ->with($command, [$firstStamp, $secondStamp])
            ->willReturn($envelope);

        $stampsAwareMessageBus = new StampsAwareMessageBus($messageBus);

        $stampsAwareMessageBus->addStamp($firstStamp);
        $stampsAwareMessageBus->dispatch($command, [$secondStamp]);
    }
}
