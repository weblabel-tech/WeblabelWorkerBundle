<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Weblabel\WorkerBundle\DependencyInjection\WeblabelWorkerExtension;
use Weblabel\WorkerBundle\Retry\CommandRetryStrategy;

final class RetryStrategyPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('weblabel_worker')) {
            return;
        }

        /** @var WeblabelWorkerExtension $extension */
        $extension = $container->getExtension('weblabel_worker');
        $config = $extension->getConfig();

        $locatorDefinition = $container->getDefinition('messenger.retry_strategy_locator');
        /** @var Reference[] $retryStrategyReferences */
        $retryStrategyReferences = $locatorDefinition->getArgument(0);

        /** @var Reference[] $transportRetryReferences */
        $transportRetryReferences = [];
        foreach ($retryStrategyReferences as $transportId => $retryStrategyReference) {
            $retryServiceId = \sprintf('weblabel_worker.retry.command_retry_strategy.%s', $transportId);
            $container
                ->register($retryServiceId, CommandRetryStrategy::class)
                ->setArgument(0, $retryStrategyReference)
                ->setArgument(1, $config['commands']);

            $transportRetryReferences[$transportId] = new Reference($retryServiceId);
        }

        $container
            ->getDefinition('messenger.retry_strategy_locator')
            ->replaceArgument(0, $transportRetryReferences);
    }
}
