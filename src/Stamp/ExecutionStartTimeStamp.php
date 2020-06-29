<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

final class ExecutionStartTimeStamp implements StampInterface
{
    private float $startTime;

    public function __construct(float $startTime)
    {
        $this->startTime = $startTime;
    }

    public function getStartTime(): float
    {
        return $this->startTime;
    }
}
