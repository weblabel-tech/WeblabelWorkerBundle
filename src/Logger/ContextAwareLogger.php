<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Logger;

use Psr\Log\LoggerInterface;

interface ContextAwareLogger extends LoggerInterface
{
    public function getContext(): array;

    public function setContext(array $context): void;
}
