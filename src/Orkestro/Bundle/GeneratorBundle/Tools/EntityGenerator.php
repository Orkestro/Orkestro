<?php

namespace Orkestro\Bundle\GeneratorBundle\Tools;

use Doctrine\ORM\Mapping\ClassMetadataInfo;

class EntityGenerator extends \Doctrine\ORM\Tools\EntityGenerator
{
    /**
     * @var string
     */
    protected static $classTemplate =
        '<?php

<namespace>

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslatable;

<entityAnnotation>
<entityClassName>
{
<entityBody>
}
';

    /**
     * Generates a PHP5 Doctrine 2 entity class from the given ClassMetadataInfo instance.
     *
     * @param ClassMetadataInfo $metadata
     * @param $translationClass
     *
     * @return string
     */
    public function generateTranslatableEntityClass(ClassMetadataInfo $metadata, $translationClass)
    {
        $placeHolders = array(
            '<namespace>',
            '<entityAnnotation>',
            '<entityClassName>',
            '<entityBody>'
        );

        $replacements = array(
            $this->generateEntityNamespace($metadata),
            $this->generateEntityDocBlock($metadata),
            $this->generateEntityClassName($metadata),
            $this->generateTranslatableEntityBody($metadata, $translationClass)
        );

        $code = str_replace($placeHolders, $replacements, self::$classTemplate);

        return str_replace('<spaces>', $this->spaces, $code);
    }

    /**
     * @param ClassMetadataInfo $metadata
     * @param $translationClass
     *
     * @return string
     */
    protected function generateTranslatableEntityBody(ClassMetadataInfo $metadata, $translationClass)
    {
        $fieldMappingProperties = $this->generateEntityFieldMappingProperties($metadata);
        $associationMappingProperties = $this->generateEntityAssociationMappingProperties($metadata);
        $stubMethods = $this->generateEntityStubMethods ? $this->generateTranslatableEntityStubMethods($metadata, $translationClass) : null;
        $lifecycleCallbackMethods = $this->generateEntityLifecycleCallbackMethods($metadata);

        $code = array();

        if ($fieldMappingProperties) {
            $fieldMappingProperties .= sprintf('
    /**
     * @Prezent\Translations(targetEntity="%1$s")
     */
    protected $translations;

    /**
     * @Prezent\CurrentLocale
     */
    private $currentLocale;

    /**
     * @var %1$s $currentTranslation
     */
    private $currentTranslation;
            ', $translationClass);

            $code[] = $fieldMappingProperties;
        }

        if ($associationMappingProperties) {
            $code[] = $associationMappingProperties;
        }

        $metadata->associationMappings[] = array(
            'type' => ClassMetadataInfo::TO_MANY,
            'fieldName' => 'translations',
        );

        $code[] = $this->generateEntityConstructor($metadata);

        if ($stubMethods) {
            $code[] = $stubMethods;
        }

        if ($lifecycleCallbackMethods) {
            $code[] = $lifecycleCallbackMethods;
        }

        return implode("\n", $code);
    }

    /**
     * @param ClassMetadataInfo $metadata
     * @param $translationClass
     *
     * @return string
     */
    protected function generateTranslatableEntityStubMethods(ClassMetadataInfo $metadata, $translationClass)
    {
        $methods = array();

        foreach ($metadata->fieldMappings as $fieldMapping) {
            if ( ! isset($fieldMapping['id']) || ! $fieldMapping['id'] || $metadata->generatorType == ClassMetadataInfo::GENERATOR_TYPE_NONE) {
                if ($code = $this->generateEntityStubMethod($metadata, 'set', $fieldMapping['fieldName'], $fieldMapping['type'])) {
                    $methods[] = $code;
                }
            }

            if ($code = $this->generateEntityStubMethod($metadata, 'get', $fieldMapping['fieldName'], $fieldMapping['type'])) {
                $methods[] = $code;
            }
        }

        foreach ($metadata->associationMappings as $associationMapping) {
            if ($associationMapping['type'] & ClassMetadataInfo::TO_ONE) {
                $nullable = $this->isAssociationIsNullable($associationMapping) ? 'null' : null;
                if ($code = $this->generateEntityStubMethod($metadata, 'set', $associationMapping['fieldName'], $associationMapping['targetEntity'], $nullable)) {
                    $methods[] = $code;
                }
                if ($code = $this->generateEntityStubMethod($metadata, 'get', $associationMapping['fieldName'], $associationMapping['targetEntity'])) {
                    $methods[] = $code;
                }
            } elseif ($associationMapping['type'] & ClassMetadataInfo::TO_MANY) {
                if ($code = $this->generateEntityStubMethod($metadata, 'add', $associationMapping['fieldName'], $associationMapping['targetEntity'])) {
                    $methods[] = $code;
                }
                if ($code = $this->generateEntityStubMethod($metadata, 'remove', $associationMapping['fieldName'], $associationMapping['targetEntity'])) {
                    $methods[] = $code;
                }
                if ($code = $this->generateEntityStubMethod($metadata, 'get', $associationMapping['fieldName'], 'Doctrine\Common\Collections\Collection')) {
                    $methods[] = $code;
                }
            }
        }

        $methods[] = sprintf('
    public function translate($locale = null)
    {
        if (null === $locale) {
            $locale = $this->currentLocale;
        }

        if (!$locale) {
            throw new \RuntimeException(\'No locale has been set and currentLocale is empty\');
        }

        if ($this->currentTranslation && $this->currentTranslation->getLocale() === $locale) {
            return $this->currentTranslation;
        }

        if (!$translation = $this->translations->get($locale)) {
            $translation = new %s();
            $translation->setLocale($locale);
            $this->addTranslation($translation);
        }

        $this->currentTranslation = $translation;
        return $translation;
    }
        ', $translationClass);

        return implode("\n\n", $methods);
    }

    /**
     * Generates a PHP5 Doctrine 2 entity class from the given ClassMetadataInfo instance.
     *
     * @param ClassMetadataInfo $metadata
     * @param $translatableClass
     *
     * @return string
     */
    public function generateTranslationEntityClass(ClassMetadataInfo $metadata, $translatableClass)
    {
        $placeHolders = array(
            '<namespace>',
            '<entityAnnotation>',
            '<entityClassName>',
            '<entityBody>'
        );

        $replacements = array(
            $this->generateEntityNamespace($metadata),
            $this->generateEntityDocBlock($metadata),
            $this->generateEntityClassName($metadata),
            $this->generateTranslationEntityBody($metadata, $translatableClass)
        );

        $code = str_replace($placeHolders, $replacements, self::$classTemplate);

        return str_replace('<spaces>', $this->spaces, $code);
    }

    /**
     * @param ClassMetadataInfo $metadata
     * @param $translatableClass
     *
     * @return string
     */
    protected function generateTranslationEntityBody(ClassMetadataInfo $metadata, $translatableClass)
    {
        $fieldMappingProperties = $this->generateEntityFieldMappingProperties($metadata);
        $associationMappingProperties = $this->generateEntityAssociationMappingProperties($metadata);
        $stubMethods = $this->generateEntityStubMethods ? $this->generateEntityStubMethods($metadata) : null;
        $lifecycleCallbackMethods = $this->generateEntityLifecycleCallbackMethods($metadata);

        $code = array();

        if ($fieldMappingProperties) {
            $fieldMappingProperties .= sprintf('
    /**
     * @Prezent\Translatable(targetEntity="%s")
     */
    protected $translatable;
            ', $translatableClass);

            $code[] = $fieldMappingProperties;
        }

        if ($associationMappingProperties) {
            $code[] = $associationMappingProperties;
        }

        $code[] = $this->generateEntityConstructor($metadata);

        if ($stubMethods) {
            $code[] = $stubMethods;
        }

        if ($lifecycleCallbackMethods) {
            $code[] = $lifecycleCallbackMethods;
        }

        return implode("\n", $code);
    }
}