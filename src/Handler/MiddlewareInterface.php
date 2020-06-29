<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Handler;

use Symfony\Component\Messenger\Envelope;

interface MiddlewareInterface
{
    public function process(callable $handler, Envelope $envelope): void;
}
