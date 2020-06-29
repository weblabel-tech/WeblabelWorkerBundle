<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Weblabel\WorkerBundle\DependencyInjection\Compiler\AuditPass;
use Weblabel\WorkerBundle\DependencyInjection\Compiler\HandlerLocatorPass;
use Weblabel\WorkerBundle\DependencyInjection\Compiler\LoggerPass;
use Weblabel\WorkerBundle\DependencyInjection\Compiler\MessageBusPass;
use Weblabel\WorkerBundle\DependencyInjection\Compiler\RetryStrategyPass;
use Weblabel\WorkerBundle\Handler\MiddlewareInterface;

class WeblabelWorkerBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->registerForAutoconfiguration(MiddlewareInterface::class)->addTag('weblabel_worker.handler.middleware');

        $container->addCompilerPass(new RetryStrategyPass());
        $container->addCompilerPass(new MessageBusPass());
        $container->addCompilerPass(new HandlerLocatorPass());
        $container->addCompilerPass(new AuditPass());
        $container->addCompilerPass(new LoggerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -48);
    }
}
