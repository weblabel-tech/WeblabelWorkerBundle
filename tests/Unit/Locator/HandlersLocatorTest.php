<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Tests\Unit\Locator;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\HandlerDescriptor;
use Symfony\Component\Messenger\Handler\HandlersLocatorInterface;
use Weblabel\WorkerBundle\Handler\MiddlewareInterface;
use Weblabel\WorkerBundle\Locator\HandlersLocator;

class HandlersLocatorTest extends TestCase
{
    public function test_handler_middleware_execution()
    {
        $command = new \stdClass();
        $envelope = new Envelope($command);

        $handler = static function () {
        };
        $descriptor = new HandlerDescriptor($handler);

        $parentHandlerLocator = $this->createMock(HandlersLocatorInterface::class);
        $parentHandlerLocator
            ->expects(self::once())
            ->method('getHandlers')
            ->with($envelope)
            ->willReturn([$descriptor]);

        $firstMiddleware = $this->createMock(MiddlewareInterface::class);
        $firstMiddleware
            ->expects(self::once())
            ->method('process')
            ->with($handler, $envelope);

        $secondMiddleware = $this->createMock(MiddlewareInterface::class);
        $secondMiddleware
            ->expects(self::once())
            ->method('process')
            ->with($handler, $envelope);

        $handlerLocator = new HandlersLocator($parentHandlerLocator, [$firstMiddleware, $secondMiddleware]);
        $handlerDescriptors = $handlerLocator->getHandlers($envelope);

        self::assertIsIterable($handlerDescriptors);

        foreach ($handlerDescriptors as $handlerDescriptor) {
            self::assertSame($descriptor, $handlerDescriptor);
        }
    }
}
