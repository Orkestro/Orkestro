<?php

namespace Orkestro\Bundle\GeneratorBundle\Generator;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sensio\Bundle\GeneratorBundle\Generator\DoctrineCrudGenerator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class OrkestroCrudGenerator extends DoctrineCrudGenerator
{
    protected $translationEntity;
    protected $translationMetadata;

//    public function setTranslationEntity($entity, ClassMetadataInfo $metadata)
    public function generateTranslatable(BundleInterface $bundle, $entity, $translationEntity, ClassMetadataInfo $metadata, ClassMetadataInfo $translationMetadata, $format, $routePrefix, $needWriteActions, $forceOverwrite)
    {
        $this->translationEntity = $translationEntity;
        $this->translationMetadata = $translationMetadata;

        $this->generate($bundle, $entity, $metadata, $format, $routePrefix, $needWriteActions, $forceOverwrite);
    }

    /**
     * Generate the CRUD controller.
     *
     * @param BundleInterface   $bundle                 A bundle object
     * @param string            $entity                 The entity relative class name
     * @param ClassMetadataInfo $metadata               The entity class metadata
     * @param string            $format                 The configuration format (xml, yaml, annotation)
     * @param string            $routePrefix            The route name prefix
     * @param array             $needWriteActions       Whether or not to generate write actions
     * @param boolean           $forceOverwrite         Overwrite already generated stuff
     *
     * @throws \RuntimeException
     */
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, $format, $routePrefix, $needWriteActions, $forceOverwrite)
    {
        $this->routePrefix = $routePrefix;
        $this->routeNamePrefix = str_replace('/', '_', $routePrefix);
        $this->actions = $needWriteActions ? array('index', 'show', 'new', 'edit', 'delete') : array('index', 'show');

        if (count($metadata->identifier) > 1) {
            throw new \RuntimeException('The CRUD generator does not support entity classes with multiple primary keys.');
        }

        if (!in_array('id', $metadata->identifier)) {
            throw new \RuntimeException('The CRUD generator expects the entity object has a primary key field named "id" with a getId() method.');
        }

        $this->entity   = $entity;
        $this->bundle   = $bundle;
        $this->metadata = $metadata;
        $this->setFormat($format);

        $this->generateControllerClass($forceOverwrite);

        $dir = sprintf('%s/Resources/views/%s', $this->bundle->getPath(), str_replace('\\', '/', $this->entity));

        if (!file_exists($dir)) {
            $this->filesystem->mkdir($dir, 0777);
        }

        $this->generateIndexView($dir);

        if (in_array('show', $this->actions)) {
            $this->generateShowView($dir);
        }

        if (in_array('new', $this->actions)) {
            $this->generateNewView($dir);
        }

        if (in_array('edit', $this->actions)) {
            $this->generateEditView($dir);
        }

        $this->generateTestClass();
        $this->generateConfiguration();
    }

    /**
     * Sets the configuration format.
     *
     * @param string $format The configuration format
     */
    private function setFormat($format)
    {
        switch ($format) {
            case 'yml':
            case 'xml':
            case 'php':
            case 'annotation':
                $this->format = $format;
                break;
            default:
                $this->format = 'yml';
                break;
        }
    }

    /**
     * Generates the index.html.twig template in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    protected function generateIndexView($dir)
    {
        $this->renderFile('crud/views/index.html.twig.twig', $dir.'/index.html.twig', array(
                'bundle'            => $this->bundle->getName(),
                'entity'            => $this->entity,
                'fields'            => $this->metadata->fieldMappings,
                'translationFields' => $this->translationMetadata->fieldMappings,
                'actions'           => $this->actions,
                'record_actions'    => $this->getRecordActions(),
                'route_prefix'      => $this->routePrefix,
                'route_name_prefix' => $this->routeNamePrefix,
            ));
    }
}