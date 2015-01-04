<?php

namespace Orkestro\Bundle\ProductBundle\Entity\Characteristic;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslatable;

/**
 * Characteristic
 *
 * @ORM\Table(name="orkestro_product_characteristic")
 * @ORM\Entity(repositoryClass="Orkestro\Bundle\ProductBundle\Entity\Characteristic\CharacteristicRepository")
 */
class Characteristic extends AbstractTranslatable
{
    const TYPE_BOOLEAN = 0;
    const TYPE_INTEGER = 1;
    const TYPE_STRING = 2;
    const TYPE_TEXT = 3;

    const SELECT_POLICY_ONE = 0;
    const SELECT_POLICY_MANY = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="select_policy", type="integer")
     */
    private $selectPolicy;

    /**
     * @ORM\OneToMany(targetEntity="Orkestro\Bundle\ProductBundle\Entity\Characteristic\Value", mappedBy="characteristic", cascade={"all"})
     */
    private $values;

    /**
     * @Prezent\Translations(targetEntity="Orkestro\Bundle\ProductBundle\Entity\Characteristic\CharacteristicTranslation")
     */
    protected $translations;

    /**
     * @Prezent\CurrentLocale
     */
    private $currentLocale;

    /**
     * @var CharacteristicTranslation $currentTranslation
     */
    private $currentTranslation;


    public function __toString()
    {
        return $this->getTitle();
    }


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
        $this->values = new ArrayCollection();
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

    public function getTitle()
    {
        return $this->translate()->getTitle();
    }

    public function setTitle($title)
    {
        $this->translate()->setTitle($title);
        return $this;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Characteristic
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set select policy
     *
     * @param integer $selectPolicy
     * @return Characteristic
     */
    public function setSelectPolicy($selectPolicy)
    {
        $this->selectPolicy = $selectPolicy;

        return $this;
    }

    /**
     * Get select policy
     *
     * @return string
     */
    public function getSelectPolicy()
    {
        return $this->selectPolicy;
    }

    /**
     * Add values
     *
     * @param \Orkestro\Bundle\ProductBundle\Entity\Characteristic\Value $values
     * @return Characteristic
     */
    public function addValue(Value $values)
    {
        $this->values[] = $values;

        return $this;
    }

    /**
     * Remove values
     *
     * @param \Orkestro\Bundle\ProductBundle\Entity\Characteristic\Value $values
     */
    public function removeValue(Value $values)
    {
        $this->values->removeElement($values);
    }

    /**
     * Get values
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getValues()
    {
        return $this->values;
    }
}
