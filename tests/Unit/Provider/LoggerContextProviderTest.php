<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Tests\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Component\Messenger\Envelope;
use Weblabel\WorkerBundle\Provider\LoggerContextProvider;
use Weblabel\WorkerBundle\Stamp\ExecutionStartTimeStamp;
use Weblabel\WorkerBundle\Stamp\HandlerIdStamp;

class LoggerContextProviderTest extends TestCase
{
    private LoggerContextProvider $loggerContextProvider;

    protected function setUp(): void
    {
        parent::setUp();
        ClockMock::register(LoggerContextProvider::class);
        $this->loggerContextProvider = new LoggerContextProvider();
    }

    /**
     * @dataProvider globalContextStampProvider
     */
    public function test_fetching_global_logger_context(array $stamps, ?string $expectedHandlerId)
    {
        $command = new \stdClass();
        $envelope = new Envelope($command, $stamps);

        $context = $this->loggerContextProvider->getGlobalContext($envelope);

        self::assertArrayHasKey('handlerId', $context);
        self::assertArrayHasKey('command', $context);
        self::assertSame(
            [
                'handlerId' => $expectedHandlerId,
                'command' => 'stdClass',
            ],
            $context
        );
    }

    public function globalContextStampProvider()
    {
        return [
            [[], null],
            [[new HandlerIdStamp('HID')], 'HID'],
        ];
    }

    public function test_fetching_handler_logger_context_without_execution_time()
    {
        $command = new \stdClass();
        $envelope = new Envelope($command);

        $context = $this->loggerContextProvider->getHandlerContext($envelope);

        self::assertArrayHasKey('executionTime', $context);
        self::assertNull($context['executionTime']);
    }

    public function test_fetching_handler_logger_context_with_execution_time()
    {
        $command = new \stdClass();
        $envelope = new Envelope($command, [new ExecutionStartTimeStamp(\microtime(true))]);

        $context = $this->loggerContextProvider->getHandlerContext($envelope);

        self::assertArrayHasKey('executionTime', $context);
        self::assertGreaterThan(0, $context['executionTime']);
    }
}
