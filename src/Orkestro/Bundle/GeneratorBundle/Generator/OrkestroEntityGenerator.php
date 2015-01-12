<?php

namespace Orkestro\Bundle\GeneratorBundle\Generator;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\Export\ClassMetadataExporter;
use Orkestro\Bundle\GeneratorBundle\Tools\EntityGenerator;
use Sensio\Bundle\GeneratorBundle\Generator\DoctrineEntityGenerator;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class OrkestroEntityGenerator extends DoctrineEntityGenerator
{
    private $filesystem;
    private $registry;

    public function __construct(Filesystem $filesystem, RegistryInterface $registry)
    {
        $this->filesystem = $filesystem;
        $this->registry = $registry;
    }

    public function generateTranslatable(BundleInterface $bundle, $entity, $translationEntity, $format, array $fields, array $translatableFields, $withRepository)
    {
        $this->generateEntity($bundle, $entity, $translationEntity, $format, $fields, $translatableFields, $withRepository);
//        $generator->generate($translationBundle, $translationEntity, $format, array_values($translatableFields), false);
    }

    public function generateEntity(BundleInterface $bundle, $entity, $translationEntity, $format, array $fields, array $translatableFields, $withRepository)
    {
        // configure the bundle (needed if the bundle does not contain any Entities yet)
        $config = $this->registry->getManager(null)->getConfiguration();
        $config->setEntityNamespaces(array_merge(
                array($bundle->getName() => $bundle->getNamespace().'\\Entity'),
                $config->getEntityNamespaces()
            ));

        $entityClass = $this->registry->getAliasNamespace($bundle->getName()).'\\'.$entity;
        $entityPath = $bundle->getPath().'/Entity/'.str_replace('\\', '/', $entity).'.php';
        if (file_exists($entityPath)) {
            throw new \RuntimeException(sprintf('Entity "%s" already exists.', $entityClass));
        }

        $class = new ClassMetadataInfo($entityClass);
        if ($withRepository) {
            $class->customRepositoryClassName = $entityClass.'Repository';
        }
        $class->mapField(array('fieldName' => 'id', 'type' => 'integer', 'id' => true));
        $class->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_AUTO);
        foreach ($fields as $field) {
            $class->mapField($field);
        }

        $entityGenerator = $this->getEntityGenerator();
        $entityGenerator->setClassToExtend('Prezent\Doctrine\Translatable\Entity\AbstractTranslatable');
        if ('annotation' === $format) {
            $entityGenerator->setGenerateAnnotations(true);
            $entityCode = $entityGenerator->generateTranslatableEntityClass($class, $translationEntity);
            $mappingPath = $mappingCode = false;
        } else {
            $cme = new ClassMetadataExporter();
            $exporter = $cme->getExporter('yml' == $format ? 'yaml' : $format);
            $mappingPath = $bundle->getPath().'/Resources/config/doctrine/'.str_replace('\\', '.', $entity).'.orm.'.$format;

            if (file_exists($mappingPath)) {
                throw new \RuntimeException(sprintf('Cannot generate entity when mapping "%s" already exists.', $mappingPath));
            }

            $mappingCode = $exporter->exportClassMetadata($class);
            $entityGenerator->setGenerateAnnotations(false);
            $entityCode = $entityGenerator->generateEntityClass($class, $translationEntity);
        }

        $this->filesystem->mkdir(dirname($entityPath));
        file_put_contents($entityPath, $entityCode);

        if ($mappingPath) {
            $this->filesystem->mkdir(dirname($mappingPath));
            file_put_contents($mappingPath, $mappingCode);
        }

        if ($withRepository) {
            $path = $bundle->getPath().str_repeat('/..', substr_count(get_class($bundle), '\\'));
            $this->getRepositoryGenerator()->writeEntityRepositoryClass($class->customRepositoryClassName, $path);
        }
    }

    protected function getEntityGenerator()
    {
        $entityGenerator = new EntityGenerator();
        $entityGenerator->setGenerateAnnotations(false);
        $entityGenerator->setGenerateStubMethods(true);
        $entityGenerator->setRegenerateEntityIfExists(false);
        $entityGenerator->setUpdateEntityIfExists(true);
        $entityGenerator->setNumSpaces(4);
        $entityGenerator->setAnnotationPrefix('ORM\\');

        return $entityGenerator;
    }
}