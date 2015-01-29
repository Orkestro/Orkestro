<?php

namespace Orkestro\Bundle\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
abstract class AbstractOrkestroConfiguration implements ConfigurationInterface
{
    private $bundleAlias;
    private $defaultDbDriver;

    public function __construct($bundleAlias, $defaultDbDriver)
    {
        $this->bundleAlias = $bundleAlias;
        $this->defaultDbDriver = $defaultDbDriver;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->bundleAlias);

        $supportedDbDrivers = Configuration::getSupportedDbDrivers();

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
                    ->defaultValue($this->defaultDbDriver)
                ->end()
                ->scalarNode('model_manager_name')->defaultNull()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
