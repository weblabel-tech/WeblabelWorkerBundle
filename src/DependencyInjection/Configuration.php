<?php

declare(strict_types=1);

namespace Weblabel\WorkerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('weblabel_worker');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->append($this->getMiddlewareNode())
            ->append($this->getCommandsNode())
            ->append($this->getLoggerNode());

        return $treeBuilder;
    }

    private function getMiddlewareNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('middleware');

        $node = $treeBuilder->getRootNode()
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('audit')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->isRequired()
                            ->defaultTrue()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }

    private function getCommandsNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('commands');

        $node = $treeBuilder->getRootNode()
            ->fixXmlConfig('command')
            ->useAttributeAsKey('name')
            ->arrayPrototype()
                ->addDefaultsIfNotSet()
                ->children()
                    ->integerNode('max_retries')
                        ->isRequired()
                        ->min(0)
                    ->end()
                    ->integerNode('delay')
                        ->defaultValue(1000)
                        ->min(0)
                    ->end()
                    ->floatNode('multiplier')
                        ->defaultValue(2)
                        ->min(1)
                    ->end()
                    ->integerNode('max_delay')
                        ->defaultValue(0)
                        ->min(0)
                    ->end()
                ->end()
            ->end();

        return $node;
    }

    private function getLoggerNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('logger');

        $node = $treeBuilder->getRootNode()
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('default_logger')
                    ->isRequired()
                    ->defaultValue('weblabel_worker.logger')
                ->end()
            ->end();

        return $node;
    }
}
