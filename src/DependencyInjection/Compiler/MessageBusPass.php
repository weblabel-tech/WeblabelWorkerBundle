<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Weblabel\WorkerBundle\Bus\StampsAwareMessageBus;

final class MessageBusPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $busServiceIds = $container->findTaggedServiceIds('messenger.bus');
        foreach ($busServiceIds as $busServiceId => $attributes) {
            $newMessageBus = 'weblabel_worker.bus.'.\str_replace('messenger.bus.', '', $busServiceId);
            $container
                ->register($newMessageBus, StampsAwareMessageBus::class)
                ->setArgument(0, new Reference($busServiceId));
        }
    }
}
