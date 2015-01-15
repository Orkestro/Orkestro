<?php

namespace Orkestro\Bundle\ProductBundle\Entity\Characteristic;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslatable;

/**
 * Value
 *
 * @ORM\Table(name="orkestro_product_characteristic_value")
 * @ORM\Entity(repositoryClass="Orkestro\Bundle\ProductBundle\Entity\Characteristic\ValueRepository")
 */
class Value extends AbstractTranslatable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Orkestro\Bundle\ProductBundle\Entity\Characteristic\Characteristic", inversedBy="values")
     * @ORM\JoinColumn(name="characteristic_id", referencedColumnName="id")
     */
    protected $characteristic;

    /**
     * @Prezent\Translations(targetEntity="Orkestro\Bundle\ProductBundle\Entity\Characteristic\ValueTranslation")
     */
    protected $translations;

    /**
     * @Prezent\CurrentLocale
     */
    private $currentLocale;

    /**
     * @var ValueTranslation $currentTranslation
     */
    private $currentTranslation;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function translate($locale = null)
    {
        if (null === $locale) {
            $locale = $this->currentLocale;
        }

        if (!$locale) {
            throw new \RuntimeException('No locale has been set and currentLocale is empty');
        }

        if ($this->currentTranslation && $this->currentTranslation->getLocale() === $locale) {
            return $this->currentTranslation;
        }

        if (!$translation = $this->translations->get($locale)) {
            $translation = new CharacteristicTranslation();
            $translation->setLocale($locale);
            $this->addTranslation($translation);
        }

        $this->currentTranslation = $translation;
        return $translation;
    }

    public function getValue()
    {
        return $this->translate()->getValue();
    }

    public function setValue($value)
    {
        $this->translate()->setValue($value);
        return $this;
    }

    /**
     * Set characteristic
     *
     * @param \Orkestro\Bundle\ProductBundle\Entity\Characteristic\Characteristic $characteristic
     * @return Value
     */
    public function setCharacteristic(Characteristic $characteristic = null)
    {
        $this->characteristic = $characteristic;

        return $this;
    }

    /**
     * Get characteristic
     *
     * @return \Orkestro\Bundle\ProductBundle\Entity\Characteristic\Characteristic 
     */
    public function getCharacteristic()
    {
        return $this->characteristic;
    }
}
