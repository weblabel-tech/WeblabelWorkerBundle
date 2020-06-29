<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Stamp;

final class HandlerIdStamp implements ForwardableStampInterface
{
    private string $handlerId;

    public function __construct(string $handlerId)
    {
        $this->handlerId = $handlerId;
    }

    public function getHandlerId(): string
    {
        return $this->handlerId;
    }

    public static function create(?string $parentHandlerId = null): self
    {
        $handlerId = \sprintf('HID-%s', \bin2hex(\random_bytes(8)));
        if (null !== $parentHandlerId) {
            $handlerId = \sprintf('%s/%s', $parentHandlerId, $handlerId);
        }

        return new self($handlerId);
    }
}
