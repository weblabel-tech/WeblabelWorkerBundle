<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Middleware;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Weblabel\WorkerBundle\Logger\ContextAwareLogger;
use Weblabel\WorkerBundle\Provider\LoggerContextProvider;

final class HandlerAuditMiddleware implements MiddlewareInterface
{
    private ContextAwareLogger $logger;

    private LoggerContextProvider $contextProvider;

    public function __construct(ContextAwareLogger $logger, LoggerContextProvider $contextProvider)
    {
        $this->logger = $logger;
        $this->contextProvider = $contextProvider;
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $context = $this->contextProvider->getGlobalContext($envelope);
        $this->logger->setContext($context);

        if ($envelope->last(ReceivedStamp::class)) {
            $this->logger->info('Handling command');
            $this->logger->debug('Command properties', $this->getProperties($envelope->getMessage()));
        }

        $envelope = $stack->next()->handle($envelope, $stack);

        if ($envelope->last(HandledStamp::class)) {
            $this->logger->info(
                'Command handling completed',
                $this->contextProvider->getHandlerContext($envelope)
            );
        }

        return $envelope;
    }

    private function getProperties(object $command): array
    {
        $reflection = new \ReflectionClass($command);
        $properties = $reflection->getProperties();

        $result = [];
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $result[$property->getName()] = $property->getValue($command);
            $property->setAccessible(false);
        }

        return $result;
    }
}
