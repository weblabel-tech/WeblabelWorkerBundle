<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Tests\Unit\Retry\Configuration;

use PHPUnit\Framework\TestCase;
use Weblabel\WorkerBundle\Retry\Configuration\RetryConfiguration;

class RetryConfigurationTest extends TestCase
{
    public function test_negative_delay()
    {
        $this->expectException(\InvalidArgumentException::class);

        new RetryConfiguration(1, -1, 1.0, 2);
    }

    public function test_zero_multiplier()
    {
        $this->expectException(\InvalidArgumentException::class);

        new RetryConfiguration(1, 1, 0, 2);
    }

    public function test_negative_max_delay()
    {
        $this->expectException(\InvalidArgumentException::class);

        new RetryConfiguration(1, 1, 1.0, -1);
    }
}
