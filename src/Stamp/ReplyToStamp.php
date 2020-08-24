<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

final class ReplyToStamp implements StampInterface
{
    private string $replyTo;

    public function __construct(string $replyTo)
    {
        $this->replyTo = $replyTo;
    }

    public function getReplyTo(): string
    {
        return $this->replyTo;
    }
}
