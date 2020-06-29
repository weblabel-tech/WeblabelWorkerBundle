<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Tests\Unit\Middleware;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Middleware\StackMiddleware;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Symfony\Component\Messenger\Test\Middleware\MiddlewareTestCase;
use Weblabel\WorkerBundle\Logger\ContextAwareLogger;
use Weblabel\WorkerBundle\Middleware\HandlerAuditMiddleware;
use Weblabel\WorkerBundle\Provider\LoggerContextProvider;

class HandlerAuditMiddlewareTest extends MiddlewareTestCase
{
    public function test_logging_info_about_received_command()
    {
        $stack = $this->getStackMock();

        $logger = $this->createMock(ContextAwareLogger::class);
        $logger
            ->expects(self::once())
            ->method('setContext')
            ->with(['foo' => 'bar']);

        $logger
            ->expects(self::exactly(2))
            ->method('info')
            ->withConsecutive(
                ['Handling command'],
                ['Command handling completed', ['bar' => 'baz']],
            );
        $logger
            ->expects(self::once())
            ->method('debug')
            ->with('Command properties', ['baz' => 'qux']);

        $command = new class() {
            private $baz = 'qux';
        };
        $envelope = new Envelope($command, [new ReceivedStamp('transport')]);

        $contextProvider = $this->createMock(LoggerContextProvider::class);
        $contextProvider
            ->expects(self::once())
            ->method('getGlobalContext')
            ->with($envelope)
            ->willReturn(['foo' => 'bar']);

        $contextProvider
            ->expects(self::once())
            ->method('getHandlerContext')
            ->willReturn(['bar' => 'baz']);

        $middleware = new HandlerAuditMiddleware($logger, $contextProvider);
        $middleware->handle($envelope, $stack);
    }

    protected function getStackMock(bool $nextIsCalled = true)
    {
        $nextMiddleware = $this->getMockBuilder(MiddlewareInterface::class)->getMock();
        $nextMiddleware
            ->expects($this->once())
            ->method('handle')
            ->willReturnCallback(
                function (Envelope $envelope, StackInterface $stack): Envelope {
                    return $envelope->with(new HandledStamp('result', 'handler'));
                }
            );

        return new StackMiddleware($nextMiddleware);
    }
}
