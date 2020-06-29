<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class WeblabelWorkerExtension extends Extension
{
    private array $config = [];

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.xml');

        $configuration = new Configuration();
        $this->config = $this->processConfiguration($configuration, $configs);
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}
