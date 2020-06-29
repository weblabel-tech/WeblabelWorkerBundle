<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Tests\Unit\Middleware;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Test\Middleware\MiddlewareTestCase;
use Weblabel\WorkerBundle\Middleware\AddHandlerIdMiddleware;
use Weblabel\WorkerBundle\Stamp\HandlerIdStamp;

class AddHandlerIdMiddlewareTest extends MiddlewareTestCase
{
    public function test_adding_handler_id()
    {
        $stack = $this->getStackMock();

        $middleware = new AddHandlerIdMiddleware();
        $envelope = new Envelope(new \stdClass());

        $updatedEnvelope = $middleware->handle($envelope, $stack);
        $stamps = $updatedEnvelope->all(HandlerIdStamp::class);

        self::assertCount(1, $stamps);
        self::assertContainsOnlyInstancesOf(HandlerIdStamp::class, $stamps);
    }

    public function test_adding_handler_id_with_existing_parent_handler_id()
    {
        $stack = $this->getStackMock();

        $middleware = new AddHandlerIdMiddleware();
        $envelope = new Envelope(new \stdClass(), [new HandlerIdStamp('HID')]);

        $updatedEnvelope = $middleware->handle($envelope, $stack);
        $stamps = $updatedEnvelope->all(HandlerIdStamp::class);

        self::assertCount(2, $stamps);
        self::assertContainsOnlyInstancesOf(HandlerIdStamp::class, $stamps);
    }
}
