<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Weblabel\WorkerBundle\Locator\HandlersLocator;

final class HandlerLocatorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $busServiceIds = $container->findTaggedServiceIds('messenger.bus');
        foreach ($busServiceIds as $busServiceId => $attributes) {
            $handleMessageMiddlewareId = $busServiceId.'.middleware.handle_message';
            if ($container->has($handleMessageMiddlewareId)) {
                $defaultHandlerLocatorId = $busServiceId.'.messenger.handlers_locator';
                $newHandlerLocatorId = 'weblabel_worker.locator.'.\str_replace('messenger.bus.', '', $busServiceId).'.handlers_locator';

                $middlewareServiceIds = $container->findTaggedServiceIds('weblabel_worker.handler.middleware');
                $handlerMiddlewareReferences = $this->getHandlerMiddlewareReferences($middlewareServiceIds);

                $container
                    ->register($newHandlerLocatorId, HandlersLocator::class)
                    ->setArgument(0, new Reference($defaultHandlerLocatorId))
                    ->setArgument(1, $handlerMiddlewareReferences);

                $container
                    ->getDefinition($handleMessageMiddlewareId)
                    ->replaceArgument(0, new Reference($newHandlerLocatorId));
            }
        }
    }

    private function getHandlerMiddlewareReferences(array $serviceIds): array
    {
        $references = [];
        foreach ($serviceIds as $serviceId => $attributes) {
            $references[] = new Reference($serviceId);
        }

        return $references;
    }
}
