<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\DependencyInjection\Compiler;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Weblabel\WorkerBundle\DependencyInjection\WeblabelWorkerExtension;

final class LoggerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('weblabel_worker')) {
            return;
        }

        /** @var WeblabelWorkerExtension $extension */
        $extension = $container->getExtension('weblabel_worker');
        $config = $extension->getConfig();
        $defaultLogger = $config['logger']['default_logger'];
        if (null === $defaultLogger) {
            return;
        }

        $container->setAlias(LoggerInterface::class, $defaultLogger);

        $retryListenerId = 'messenger.retry.send_failed_message_for_retry_listener';
        if ($container->hasDefinition($retryListenerId)) {
            $retryListener = $container->getDefinition($retryListenerId);
            $retryListener->setArgument(2, new Reference($defaultLogger));
        }

        $failureTransportListenerId = 'messenger.failure.send_failed_message_to_failure_transport_listener';
        if ($container->hasDefinition($failureTransportListenerId)) {
            $failureTransportListener = $container->getDefinition($failureTransportListenerId);
            $failureTransportListener->setArgument(1, new Reference($defaultLogger));
        }
    }
}
