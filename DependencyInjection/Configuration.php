<?php

namespace Kolyya\PhotoBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kolyya_photo');

        $rootNode
            ->children()
                ->append($this->createObjectsNode())
            ->end()
        ;

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }

    private function createObjectsNode()
    {
        return $this->createNode('objects')
            ->normalizeKeys(false)
            ->useAttributeAsKey('name')
            ->arrayPrototype()
                ->children()
                    ->scalarNode('object_class')->cannotBeEmpty()->end()
                    ->scalarNode('photo_class')->cannotBeEmpty()->end()
                    ->scalarNode('check_permissions')->defaultValue('kolyya_photo.check_permissions')->end()
                    ->scalarNode('path')->cannotBeEmpty()->end()
                    ->scalarNode('manager_format')->cannotBeEmpty()->end()
                    ->append($this->createFormatsNode())
                ->end()
            ->end()
        ;
    }

    private function createFormatsNode()
    {
        return $this->createNode('formats')
            ->normalizeKeys(false)
            ->useAttributeAsKey('name')
            ->arrayPrototype()
                ->children()
                    ->variableNode('resize')->defaultFalse()->end()
                    ->scalarNode('heighten')->defaultFalse()->end()
                ->end()
            ->end()
        ;
    }

    private function createNode($name)
    {
        return $this->createTreeBuilder()->root($name);
    }

    private function createTreeBuilder()
    {
        return new TreeBuilder();
    }
}
