<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Retry\Configuration;

final class RetryConfiguration
{
    private int $maxRetries;

    private int $delayMilliseconds;

    private float $multiplier;

    private int $maxDelayMilliseconds;

    public function __construct(int $maxRetries, int $delayMilliseconds, float $multiplier, int $maxDelayMilliseconds)
    {
        $this->maxRetries = $maxRetries;

        if ($delayMilliseconds < 0) {
            throw new \InvalidArgumentException(\sprintf('Delay must be greater than or equal to zero: "%s" passed.', $delayMilliseconds));
        }
        $this->delayMilliseconds = $delayMilliseconds;

        if ($multiplier < 1) {
            throw new \InvalidArgumentException(\sprintf('Multiplier must be greater than zero: "%s" passed.', $multiplier));
        }
        $this->multiplier = $multiplier;

        if ($maxDelayMilliseconds < 0) {
            throw new \InvalidArgumentException(\sprintf('Max delay must be greater than or equal to zero: "%s" passed.', $maxDelayMilliseconds));
        }
        $this->maxDelayMilliseconds = $maxDelayMilliseconds;
    }

    public function getMaxRetries(): int
    {
        return $this->maxRetries;
    }

    public function getDelayMilliseconds(): int
    {
        return $this->delayMilliseconds;
    }

    public function getMultiplier(): float
    {
        return $this->multiplier;
    }

    public function getMaxDelayMilliseconds(): int
    {
        return $this->maxDelayMilliseconds;
    }

    public static function fromArray(array $configuration): self
    {
        return new self(
            $configuration['max_retries'],
            $configuration['delay'],
            $configuration['multiplier'],
            $configuration['max_delay']
        );
    }
}
