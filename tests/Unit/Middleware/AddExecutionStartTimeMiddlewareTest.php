<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Tests\Unit\Middleware;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Test\Middleware\MiddlewareTestCase;
use Weblabel\WorkerBundle\Middleware\AddExecutionStartTimeMiddleware;
use Weblabel\WorkerBundle\Stamp\ExecutionStartTimeStamp;

class AddExecutionStartTimeMiddlewareTest extends MiddlewareTestCase
{
    public function test_adding_execution_start_time()
    {
        $stack = $this->getStackMock();

        $middleware = new AddExecutionStartTimeMiddleware();
        $envelope = new Envelope(new \stdClass());

        $updatedEnvelope = $middleware->handle($envelope, $stack);
        $stamps = $updatedEnvelope->all(ExecutionStartTimeStamp::class);

        self::assertCount(1, $stamps);
        self::assertContainsOnlyInstancesOf(ExecutionStartTimeStamp::class, $stamps);
    }
}
