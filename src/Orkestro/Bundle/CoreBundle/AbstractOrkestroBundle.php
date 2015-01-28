<?php

namespace Orkestro\Bundle\CoreBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

abstract class AbstractOrkestroBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $mappings = array(
            realpath($this->getPath().'/Resources/config/doctrine/model') => $this->getNamespace().'\Model',
        );

        $this->buildOrmCompilerPass($container, $mappings);
    }

    private function buildOrmCompilerPass(ContainerBuilder $container, array $mappings)
    {
        if (!class_exists('Doctrine\ORM\Version')) {
            return;
        }

        $doctrineOrmCompiler = $this->findDoctrineOrmCompiler();
        if (!$doctrineOrmCompiler) {
            return;
        }

        $bundleNameUnderscored = Container::underscore($this->getBundleBasename());

        $container->addCompilerPass(
            $doctrineOrmCompiler::createXmlMappingDriver(
                $mappings,
                array(
                    $bundleNameUnderscored.'.model_manager_name',
                ),
                $bundleNameUnderscored.'.backend_type_orm',
                array(
                    $this->getName() => $this->getNamespace().'\Model',
                )
            )
        );
    }

    private function findDoctrineOrmCompiler()
    {
        if (class_exists('Symfony\Bridge\Doctrine\DependencyInjection\CompilerPass\RegisterMappingsPass')
            && class_exists('Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass')
        ) {
            return 'Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass';
        }
        if (class_exists('Symfony\Cmf\Bundle\CoreBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass')) {
            return 'Symfony\Cmf\Bundle\CoreBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass';
        }

        return false;
    }

    private function getBundleBasename()
    {
        return substr($this->getName(), 0, -6);
    }

    public function getContainerExtension()
    {
        $extensionClassName = $this->getNamespace().'\DependencyInjection\\'.$this->getBundleBasename().'Extension';
        return new $extensionClassName;
    }
}