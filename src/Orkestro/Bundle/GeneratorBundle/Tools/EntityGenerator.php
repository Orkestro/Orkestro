<?php

namespace Orkestro\Bundle\GeneratorBundle\Tools;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\DBAL\Types\Type;
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
use Doctrine\Common\Collections\ArrayCollection;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslatable;

<entityAnnotation>
<entityClassName>
{
<entityBody>
}
';
    /**
     * @var string
     */
    protected static $translationClassTemplate =
        '<?php

<namespace>

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;

<entityAnnotation>
<entityClassName>
{
<entityBody>
}
';

    /**
     * @var string
     */
    protected static $getTranslatableMethodTemplate =
        '/**
 * <description>
 *
 * @return <variableType>
 */
public function <methodName>()
{
<spaces>return $this->translate()-><methodName>();
}';

    /**
     * @var string
     */
    protected static $setTranslatableMethodTemplate =
        '/**
 * <description>
 *
 * @param <variableType>$<variableName>
 * @return <entity>
 */
public function <methodName>(<methodTypeHint>$<variableName><variableDefault>)
{
<spaces>$this->translate()-><methodName>($<variableName>);

<spaces>return $this;
}';

    /**
     * Generates a PHP5 Doctrine 2 entity class from the given ClassMetadataInfo instance.
     *
     * @param ClassMetadataInfo $metadata
     * @param ClassMetadataInfo $translationMetadata
     *
     * @return string
     */
    public function generateTranslatableEntityClass(ClassMetadataInfo $metadata, ClassMetadataInfo $translationMetadata)
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
            $this->generateTranslatableEntityBody($metadata, $translationMetadata)
        );

        $code = str_replace($placeHolders, $replacements, self::$classTemplate);

        return str_replace('<spaces>', $this->spaces, $code);
    }

    /**
     * @param ClassMetadataInfo $metadata
     * @param ClassMetadataInfo $translationMetadata
     *
     * @return string
     */
    protected function generateTranslatableEntityBody(ClassMetadataInfo $metadata, ClassMetadataInfo $translationMetadata)
    {
        $fieldMappingProperties = '
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

';

        $fieldMappingProperties .= $this->generateEntityFieldMappingProperties($metadata);
        $associationMappingProperties = $this->generateEntityAssociationMappingProperties($metadata);
        $stubMethods = $this->generateEntityStubMethods ? $this->generateTranslatableEntityStubMethods($metadata, $translationMetadata) : null;
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
            ', $translationMetadata->name);

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
     * @param ClassMetadataInfo $translationMetadata
     *
     * @return string
     */
    protected function generateTranslatableEntityStubMethods(ClassMetadataInfo $metadata, ClassMetadataInfo $translationMetadata)
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

        foreach ($translationMetadata->fieldMappings as $fieldMapping) {
            if ($code = $this->generateTranslatableEntityStubMethod($metadata, 'set', $fieldMapping['fieldName'], $fieldMapping['type'])) {
                $methods[] = $code;
            }

            if ($code = $this->generateTranslatableEntityStubMethod($metadata, 'get', $fieldMapping['fieldName'], $fieldMapping['type'])) {
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
        ', $translationMetadata->name);

        return implode("\n\n", $methods);
    }

    /**
     * @param ClassMetadataInfo $metadata
     * @param string            $type
     * @param string            $fieldName
     * @param string|null       $typeHint
     * @param string|null       $defaultValue
     *
     * @return string
     */
    protected function generateTranslatableEntityStubMethod(ClassMetadataInfo $metadata, $type, $fieldName, $typeHint = null,  $defaultValue = null)
    {
        $methodName = $type . Inflector::classify($fieldName);
        if (in_array($type, array("add", "remove"))) {
            $methodName = Inflector::singularize($methodName);
        }

        if ($this->hasMethod($methodName, $metadata)) {
            return '';
        }
        $this->staticReflection[$metadata->name]['methods'][] = $methodName;

        $var = sprintf('%sTranslatableMethodTemplate', $type);
        $template = self::$$var;

        $methodTypeHint = null;
        $types          = Type::getTypesMap();
        $variableType   = $typeHint ? $this->getType($typeHint) . ' ' : null;

        if ($typeHint && ! isset($types[$typeHint])) {
            $variableType   =  '\\' . ltrim($variableType, '\\');
            $methodTypeHint =  '\\' . $typeHint . ' ';
        }

        $replacements = array(
            '<description>'       => ucfirst($type) . ' ' . $fieldName,
            '<methodTypeHint>'    => $methodTypeHint,
            '<variableType>'      => $variableType,
            '<variableName>'      => Inflector::camelize($fieldName),
            '<methodName>'        => $methodName,
            '<fieldName>'         => $fieldName,
            '<variableDefault>'   => ($defaultValue !== null ) ? (' = '.$defaultValue) : '',
            '<entity>'            => $this->getClassName($metadata)
        );

        $method = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $template
        );

        return $this->prefixCodeWithSpaces($method);
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

        $code = str_replace($placeHolders, $replacements, self::$translationClassTemplate);

        return str_replace('<spaces>', $this->spaces, $code);
    }

    /**
     * @param ClassMetadataInfo $metadata
     * @param $translatableClass
     *
     * @return string
     */
    protected function generateTranslationEntityBody(ClassMetadataInfo $metadata, ClassMetadataInfo $translatableMetadata)
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
            ', $translatableMetadata->name);

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