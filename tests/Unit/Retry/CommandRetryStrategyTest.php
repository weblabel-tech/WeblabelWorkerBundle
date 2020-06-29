<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Tests\Unit\Retry;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Retry\RetryStrategyInterface;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;
use Weblabel\WorkerBundle\Retry\CommandRetryStrategy;

class CommandRetryStrategyTest extends TestCase
{
    /**
     * @dataProvider retryDataProvider
     */
    public function test_command_retry_strategy_for_configured_command(int $retries, bool $expectedResult)
    {
        $message = new Envelope(new \stdClass(), [new RedeliveryStamp(1)]);
        $retryStrategy = $this->createMock(RetryStrategyInterface::class);
        $retryStrategy
            ->expects(self::never())
            ->method('isRetryable');

        $commandRetryStrategy = new CommandRetryStrategy(
            $retryStrategy, [
                'stdClass' => [
                    'max_retries' => $retries,
                    'delay' => 1000,
                    'multiplier' => 1,
                    'max_delay' => 0,
                ],
            ]
        );
        $result = $commandRetryStrategy->isRetryable($message);

        self::assertSame($expectedResult, $result);
    }

    public function retryDataProvider(): array
    {
        return [
            [
                'retries' => 2,
                'isRetryable' => true,
            ],
            [
                'retries' => 1,
                'isRetryable' => false,
            ],
        ];
    }

    /**
     * @dataProvider delayDataProvider
     */
    public function test_delay_for_configured_command(int $delay, float $multiplier, int $maxDelay, int $retries, int $expectedResult)
    {
        $message = new Envelope(new \stdClass(), [new RedeliveryStamp($retries)]);
        $retryStrategy = $this->createMock(RetryStrategyInterface::class);
        $retryStrategy
            ->expects(self::never())
            ->method('getWaitingTime');

        $commandRetryStrategy = new CommandRetryStrategy(
            $retryStrategy, [
                'stdClass' => [
                    'max_retries' => 3,
                    'delay' => $delay,
                    'multiplier' => $multiplier,
                    'max_delay' => $maxDelay,
                ],
            ]
        );
        $delay = $commandRetryStrategy->getWaitingTime($message);

        self::assertSame($expectedResult, $delay);
    }

    public function delayDataProvider()
    {
        return [
            [
                'delay' => 1000,
                'multiplier' => 1,
                'maxDelay' => 0,
                'retryCount' => 1,
                'expectedDelay' => 1000,
            ],
            [
                'delay' => 1000,
                'multiplier' => 2,
                'maxDelay' => 0,
                'retryCount' => 1,
                'expectedDelay' => 2000,
            ],
            [
                'delay' => 1000,
                'multiplier' => 2,
                'maxDelay' => 0,
                'retryCount' => 2,
                'expectedDelay' => 4000,
            ],
            [
                'delay' => 1000,
                'multiplier' => 2,
                'maxDelay' => 2000,
                'retryCount' => 2,
                'expectedDelay' => 2000,
            ],
        ];
    }

    public function test_transport_retry_strategy_for_command_without_configuration()
    {
        $message = new Envelope(new \stdClass());
        $retryStrategy = $this->createMock(RetryStrategyInterface::class);
        $retryStrategy
            ->expects(self::once())
            ->method('isRetryable')
            ->with($message)
            ->willReturn(true);

        $commandRetryStrategy = new CommandRetryStrategy($retryStrategy);
        $result = $commandRetryStrategy->isRetryable($message);

        self::assertTrue($result);
    }

    public function test_transport_delay_for_command_without_configuration()
    {
        $message = new Envelope(new \stdClass());
        $retryStrategy = $this->createMock(RetryStrategyInterface::class);
        $retryStrategy
            ->expects(self::once())
            ->method('getWaitingTime')
            ->with($message)
            ->willReturn(1);

        $commandRetryStrategy = new CommandRetryStrategy($retryStrategy);
        $delay = $commandRetryStrategy->getWaitingTime($message);

        self::assertSame(1, $delay);
    }
}
