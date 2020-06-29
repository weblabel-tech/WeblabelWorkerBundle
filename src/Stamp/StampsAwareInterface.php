<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

interface StampsAwareInterface
{
    public function addStamp(StampInterface $stamp): void;
}
