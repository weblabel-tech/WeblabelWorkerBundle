<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Tests\Unit\Logger;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Weblabel\WorkerBundle\Logger\WorkerLogger;

class LoggerTest extends TestCase
{
    /**
     * @dataProvider logMethodProvider
     */
    public function test_logging_for_each_log_level(string $logMethod)
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock
            ->expects(self::once())
            ->method('log')
            ->with($logMethod, 'Test message', ['handlerId' => 'HID', 'command' => 'Test\Command', 'parameter' => 'value']);

        $logger = new WorkerLogger($loggerMock);
        $logger->setContext([
            'handlerId' => 'HID',
            'command' => 'Test\Command',
        ]);
        $logger->{$logMethod}('Test message', ['parameter' => 'value']);
    }

    public function logMethodProvider(): array
    {
        return [
            ['emergency'],
            ['alert'],
            ['critical'],
            ['error'],
            ['warning'],
            ['notice'],
            ['info'],
            ['debug'],
        ];
    }
}
