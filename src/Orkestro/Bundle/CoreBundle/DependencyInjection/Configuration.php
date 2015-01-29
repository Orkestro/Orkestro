<?php

namespace Orkestro\Bundle\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    public static function getSupportedDbDrivers()
    {
        return array(
            'orm',
            'mongodb',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('orkestro');

        $supportedDbDrivers = self::getSupportedDbDrivers();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('db_driver')
                    ->validate()
                        ->ifNotInArray($supportedDbDrivers)
                        ->thenInvalid('The driver %s is not supported. Please choose one of '.json_encode($supportedDbDrivers))
                    ->end()
                    ->cannotBeOverwritten()
                    ->cannotBeEmpty()
                    ->defaultValue($supportedDbDrivers[0])
                ->end()
                ->scalarNode('model_manager_name')->defaultNull()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
