<?php

namespace ImkCrudBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Class ImkCrudConfiguration.
 */
class ImkCrudConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('imk_crud');
        $treeBuilder->getRootNode()
            ->children()
            ->arrayNode('entity')
            ->arrayPrototype()
            ->arrayPrototype()
            ->children()
            ->booleanNode('crud')->defaultValue(true)->end()
            ->booleanNode('create')->defaultValue(true)->end()
            ->booleanNode('read')->defaultValue(true)->end()
            ->booleanNode('list')->defaultValue(true)->end()
            ->booleanNode('delete')->defaultValue(true)->end()
            ->booleanNode('update')->defaultValue(true)->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
