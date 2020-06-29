<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Tests\Unit\Middleware;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Middleware\StackMiddleware;
use Symfony\Component\Messenger\Stamp\SentStamp;
use Symfony\Component\Messenger\Test\Middleware\MiddlewareTestCase;
use Weblabel\WorkerBundle\Logger\ContextAwareLogger;
use Weblabel\WorkerBundle\Middleware\SenderAuditMiddleware;
use Weblabel\WorkerBundle\Stamp\HandlerIdStamp;

class SenderAuditMiddlewareTest extends MiddlewareTestCase
{
    public function test_logging_info_about_sending_command()
    {
        $stack = $this->getStackMock();

        $logger = $this->createMock(ContextAwareLogger::class);
        $logger
            ->expects(self::once())
            ->method('info')
            ->with('Command sent');

        $middleware = new SenderAuditMiddleware($logger);
        $envelope = new Envelope(new \stdClass(), [HandlerIdStamp::create()]);

        $middleware->handle($envelope, $stack);
    }

    public function test_skipping_log_if_handler_id_is_not_provided()
    {
        $stack = $this->getStackMock();

        $logger = $this->createMock(ContextAwareLogger::class);
        $logger
            ->expects(self::never())
            ->method('info');

        $middleware = new SenderAuditMiddleware($logger);
        $envelope = new Envelope(new \stdClass());

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
                    return $envelope->with(new SentStamp('class'));
                }
            );

        return new StackMiddleware($nextMiddleware);
    }
}
