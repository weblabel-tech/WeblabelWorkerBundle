<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Weblabel\WorkerBundle\DependencyInjection\WeblabelWorkerExtension;

final class AuditPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('weblabel_worker')) {
            return;
        }

        /** @var WeblabelWorkerExtension $extension */
        $extension = $container->getExtension('weblabel_worker');
        $config = $extension->getConfig();
        if (false === $config['middleware']['audit']['enabled']) {
            return;
        }

        $busServiceIds = $container->findTaggedServiceIds('messenger.bus');
        foreach ($busServiceIds as $busServiceId => $attributes) {
            $busDefinition = $container->getDefinition($busServiceId);
            /** @var Reference[] $busMiddlewareReferences */
            $busMiddlewareReferences = $busDefinition->getArgument(0)->getValues();

            $newBusMiddlewareReferences = [];
            foreach ($busMiddlewareReferences as $busMiddlewareReference) {
                if ('messenger.middleware.send_message' === (string) $busMiddlewareReference) {
                    $newBusMiddlewareReferences[] = new Reference('weblabel_worker.middleware.sender_audit');
                }

                if (\sprintf('%s.middleware.handle_message', $busServiceId) === (string) $busMiddlewareReference) {
                    $newBusMiddlewareReferences[] = new Reference('weblabel_worker.middleware.add_handler_id');
                    $newBusMiddlewareReferences[] = new Reference('weblabel_worker.middleware.add_execution_start_time');
                    $newBusMiddlewareReferences[] = new Reference('weblabel_worker.middleware.handler_audit');
                }

                $newBusMiddlewareReferences[] = $busMiddlewareReference;
            }

            $busDefinition->setArgument(0, $newBusMiddlewareReferences);
        }
    }
}
