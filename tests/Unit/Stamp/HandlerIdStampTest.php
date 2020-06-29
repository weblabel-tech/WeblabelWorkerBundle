<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Tests\Unit\Stamp;

use PHPUnit\Framework\TestCase;
use Weblabel\WorkerBundle\Stamp\ForwardableStampInterface;
use Weblabel\WorkerBundle\Stamp\HandlerIdStamp;

class HandlerIdStampTest extends TestCase
{
    public function test_handler_id_stamp_implements_forwardable_interface()
    {
        $handlerIdStamp = HandlerIdStamp::create();

        self::assertInstanceOf(ForwardableStampInterface::class, $handlerIdStamp);
    }

    public function test_random_handler_id_creation()
    {
        $handlerIdStamp = HandlerIdStamp::create();

        self::assertStringMatchesFormat('HID-%x', $handlerIdStamp->getHandlerId());
    }

    public function test_random_handler_id_creation_with_parent_handler_id()
    {
        $handlerIdStamp = HandlerIdStamp::create('parentHandlerId');

        self::assertStringMatchesFormat('parentHandlerId/HID-%x', $handlerIdStamp->getHandlerId());
    }
}
