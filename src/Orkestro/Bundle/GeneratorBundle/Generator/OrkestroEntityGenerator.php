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

    public function generateTranslatable(BundleInterface $bundle, $translatableEntity, $translationEntity, $format, array $fields, array $translatableFields, $withRepository)
    {
        $this->generateTranslatableEntity($bundle, $translatableEntity, $translationEntity, $format, $fields, $translatableFields, $withRepository);
        $this->generateTranslationEntity($bundle, $translatableEntity, $translationEntity, $format, $translatableFields);
    }

    public function generateTranslatableEntity(BundleInterface $bundle, $translatableEntity, $translationEntity, $format, array $fields, array $translatableFields, $withRepository)
    {
        // configure the bundle (needed if the bundle does not contain any Entities yet)
        $config = $this->registry->getManager(null)->getConfiguration();
        $config->setEntityNamespaces(array_merge(
                array($bundle->getName() => $bundle->getNamespace().'\\Entity'),
                $config->getEntityNamespaces()
            ));

        $translatableEntityClass = $this->registry->getAliasNamespace($bundle->getName()).'\\'.$translatableEntity;
        $translationEntityClass = $this->registry->getAliasNamespace($bundle->getName()).'\\'.$translationEntity;
        $translatableEntityPath = $bundle->getPath().'/Entity/'.str_replace('\\', '/', $translatableEntity).'.php';
        if (file_exists($translatableEntityPath)) {
            throw new \RuntimeException(sprintf('Entity "%s" already exists.', $translatableEntityClass));
        }

        $translatableClass = new ClassMetadataInfo($translatableEntityClass);
        $translationClass = new ClassMetadataInfo($translationEntityClass);
        if ($withRepository) {
            $translatableClass->customRepositoryClassName = $translatableEntityClass.'Repository';
        }
        $translatableClass->mapField(array('fieldName' => 'id', 'type' => 'integer', 'id' => true));
        $translatableClass->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_AUTO);
        foreach ($fields as $field) {
            $translatableClass->mapField($field);
        }
        foreach ($translatableFields as $translatableField) {
            $translationClass->mapField($translatableField);
        }

        $entityGenerator = $this->getEntityGenerator();
        $entityGenerator->setClassToExtend('Prezent\Doctrine\Translatable\Entity\AbstractTranslatable');
        if ('annotation' === $format) {
            $entityGenerator->setGenerateAnnotations(true);
            $entityCode = $entityGenerator->generateTranslatableEntityClass($translatableClass, $translationClass);
            $mappingPath = $mappingCode = false;
        } else {
            $cme = new ClassMetadataExporter();
            $exporter = $cme->getExporter('yml' == $format ? 'yaml' : $format);
            $mappingPath = $bundle->getPath().'/Resources/config/doctrine/'.str_replace('\\', '.', $translatableEntity).'.orm.'.$format;

            if (file_exists($mappingPath)) {
                throw new \RuntimeException(sprintf('Cannot generate entity when mapping "%s" already exists.', $mappingPath));
            }

            $mappingCode = $exporter->exportClassMetadata($translatableClass);
            $entityGenerator->setGenerateAnnotations(false);
            $entityCode = $entityGenerator->generateTranslatableEntityClass($translatableClass, $translationClass);
        }

        $this->filesystem->mkdir(dirname($translatableEntityPath));
        file_put_contents($translatableEntityPath, $entityCode);

        if ($mappingPath) {
            $this->filesystem->mkdir(dirname($mappingPath));
            file_put_contents($mappingPath, $mappingCode);
        }

        if ($withRepository) {
            $path = $bundle->getPath().str_repeat('/..', substr_count(get_class($bundle), '\\'));
            $this->getRepositoryGenerator()->writeEntityRepositoryClass($translatableClass->customRepositoryClassName, $path);
        }
    }

    public function generateTranslationEntity(BundleInterface $bundle, $translatableEntity, $translationEntity, $format, array $fields)
    {
        // configure the bundle (needed if the bundle does not contain any Entities yet)
        $config = $this->registry->getManager(null)->getConfiguration();
        $config->setEntityNamespaces(array_merge(
                array($bundle->getName() => $bundle->getNamespace().'\\Entity'),
                $config->getEntityNamespaces()
            ));

        $translationEntityClass = $this->registry->getAliasNamespace($bundle->getName()).'\\'.$translationEntity;
        $translatableEntityClass = $this->registry->getAliasNamespace($bundle->getName()).'\\'.$translatableEntity;
        $translationEntityPath = $bundle->getPath().'/Entity/'.str_replace('\\', '/', $translationEntity).'.php';
        if (file_exists($translationEntityPath)) {
            throw new \RuntimeException(sprintf('Entity "%s" already exists.', $translationEntityClass));
        }

        $translationClass = new ClassMetadataInfo($translationEntityClass);
        $translatableClass = new ClassMetadataInfo($translatableEntityClass);

        foreach ($fields as $field) {
            $translationClass->mapField($field);
        }

        $entityGenerator = $this->getEntityGenerator();
        $entityGenerator->setClassToExtend('Prezent\Doctrine\Translatable\Entity\AbstractTranslation');
        if ('annotation' === $format) {
            $entityGenerator->setGenerateAnnotations(true);
            $entityCode = $entityGenerator->generateTranslationEntityClass($translationClass, $translatableClass);
            $mappingPath = $mappingCode = false;
        } else {
            $cme = new ClassMetadataExporter();
            $exporter = $cme->getExporter('yml' == $format ? 'yaml' : $format);
            $mappingPath = $bundle->getPath().'/Resources/config/doctrine/'.str_replace('\\', '.', $translationEntity).'.orm.'.$format;

            if (file_exists($mappingPath)) {
                throw new \RuntimeException(sprintf('Cannot generate entity when mapping "%s" already exists.', $mappingPath));
            }

            $mappingCode = $exporter->exportClassMetadata($translationClass);
            $entityGenerator->setGenerateAnnotations(false);
            $entityCode = $entityGenerator->generateTranslationEntityClass($translationClass, $translatableClass);
        }

        $this->filesystem->mkdir(dirname($translationEntityPath));
        file_put_contents($translationEntityPath, $entityCode);

        if ($mappingPath) {
            $this->filesystem->mkdir(dirname($mappingPath));
            file_put_contents($mappingPath, $mappingCode);
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

    public function isReservedKeyword($keyword)
    {
        return $this->registry->getConnection()->getDatabasePlatform()->getReservedKeywordsList()->isKeyword($keyword);
    }
}