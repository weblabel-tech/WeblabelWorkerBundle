<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Locator;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\HandlersLocatorInterface;
use Weblabel\WorkerBundle\Handler\MiddlewareInterface;

final class HandlersLocator implements HandlersLocatorInterface
{
    private HandlersLocatorInterface $handlersLocator;

    /** @var iterable|MiddlewareInterface[] */
    private iterable $handlerMiddleware;

    public function __construct(HandlersLocatorInterface $handlersLocator, iterable $handlerMiddleware)
    {
        $this->handlersLocator = $handlersLocator;
        $this->handlerMiddleware = $handlerMiddleware;
    }

    public function getHandlers(Envelope $envelope): iterable
    {
        foreach ($this->handlersLocator->getHandlers($envelope) as $handlerDescriptor) {
            $handler = $handlerDescriptor->getHandler();
            foreach ($this->handlerMiddleware as $middleware) {
                $middleware->process($handler, $envelope);
            }

            yield $handlerDescriptor;
        }
    }
}
