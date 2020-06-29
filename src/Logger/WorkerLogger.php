<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Logger;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

final class WorkerLogger extends AbstractLogger implements ContextAwareLogger
{
    private LoggerInterface $logger;

    private array $context;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->context = [];
    }

    public function log($level, $message, array $context = [])
    {
        $this->logger->log($level, $message, \array_merge($context, $this->getContext()));
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function setContext(array $context): void
    {
        $this->context = $context;
    }
}
