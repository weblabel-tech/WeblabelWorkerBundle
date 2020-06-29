<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Retry;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Retry\RetryStrategyInterface;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;
use Weblabel\WorkerBundle\Retry\Configuration\RetryConfiguration;

final class CommandRetryStrategy implements RetryStrategyInterface
{
    private RetryStrategyInterface $retryStrategy;

    /** @var RetryConfiguration[] */
    private array $retryConfiguration;

    public function __construct(RetryStrategyInterface $retryStrategy, array $retryConfiguration = [])
    {
        $this->retryStrategy = $retryStrategy;
        $this->retryConfiguration = [];
        foreach ($retryConfiguration as $commandNamespace => $configuration) {
            $this->addCommandConfiguration($commandNamespace, RetryConfiguration::fromArray($configuration));
        }
    }

    public function isRetryable(Envelope $message, \Throwable $throwable = null): bool
    {
        $commandConfiguration = $this->getCommandConfiguration($message);
        if (null === $commandConfiguration) {
            return $this->retryStrategy->isRetryable($message);
        }

        $retries = RedeliveryStamp::getRetryCountFromEnvelope($message);

        return $retries < $commandConfiguration->getMaxRetries();
    }

    public function getWaitingTime(Envelope $message, \Throwable $throwable = null): int
    {
        $commandConfiguration = $this->getCommandConfiguration($message);
        if (null === $commandConfiguration) {
            return $this->retryStrategy->getWaitingTime($message);
        }

        $retries = RedeliveryStamp::getRetryCountFromEnvelope($message);

        $delayMilliseconds = $commandConfiguration->getDelayMilliseconds();
        $multiplier = $commandConfiguration->getMultiplier();
        $delay = $delayMilliseconds * ($multiplier ** $retries);

        $maxDelayMilliseconds = $commandConfiguration->getMaxDelayMilliseconds();
        if ($delay > $maxDelayMilliseconds && 0 !== $maxDelayMilliseconds) {
            return $maxDelayMilliseconds;
        }

        return (int) $delay;
    }

    private function getCommandConfiguration(Envelope $envelope): ?RetryConfiguration
    {
        $class = \get_class($envelope->getMessage());

        return $this->retryConfiguration[$class] ?? null;
    }

    private function addCommandConfiguration(string $commandNamespace, RetryConfiguration $retryConfiguration): void
    {
        $this->retryConfiguration[$commandNamespace] = $retryConfiguration;
    }
}
