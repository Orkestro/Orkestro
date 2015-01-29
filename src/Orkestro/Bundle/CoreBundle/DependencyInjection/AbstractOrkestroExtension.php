<?php

namespace Orkestro\Bundle\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;

abstract class AbstractOrkestroExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $bundleAlias = $this->getAlias();
        $bundleName = substr(Container::camelize($bundleAlias), 8);
        $configurationClassName = 'Orkestro\Bundle\\'.$bundleName.'Bundle\DependencyInjection\Configuration';

        $configuration = new $configurationClassName($bundleAlias, $container->getParameter('orkestro.db_driver'));
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter($bundleAlias.'.model_manager_name', $config['model_manager_name']);
        switch ($config['db_driver']) {
            case 'orm':
                $container->setParameter($bundleAlias.'.backend_type_orm', true);
                break;
            case 'mongodb':
                $container->setParameter($bundleAlias.'.backend_type_mongodb', true);
                break;
            default:
                break;
        }

        $reflected = new \ReflectionObject($this);
        $path = dirname($reflected->getFileName()).'/../Resources/config';

        $loader = new Loader\XmlFileLoader($container, new FileLocator($path));
        $loader->load('services.xml');
    }
}