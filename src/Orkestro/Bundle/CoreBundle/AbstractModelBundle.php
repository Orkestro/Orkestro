<?php

namespace Orkestro\Bundle\CoreBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

abstract class AbstractModelBundle extends Bundle
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

        $container->addCompilerPass(
            $doctrineOrmCompiler::createXmlMappingDriver(
                $mappings,
                array(
                    $this->getNameUnderscored().'.model_manager_name',
                ),
                $this->getNameUnderscored().'.backend_type_orm',
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

    private function getNameUnderscored()
    {
        return strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', str_replace('Bundle', '', $this->getName())));
    }
}