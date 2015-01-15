<?php

namespace Orkestro\Bundle\ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Orkestro\Bundle\ProductBundle\Entity\Characteristic\Characteristic;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslatable;

/**
 * ProductKind
 *
 * @ORM\Table(name="orkestro_product_kind")
 * @ORM\Entity(repositoryClass="Orkestro\Bundle\ProductBundle\Entity\ProductKindRepository")
 */
class ProductKind extends AbstractTranslatable
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
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Orkestro\Bundle\ProductBundle\Entity\Characteristic\Characteristic")
     * @ORM\JoinTable(name="orkestro_product_kinds_product_characteristics",
     *      joinColumns={@ORM\JoinColumn(name="product_kind_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="product_characteristic_id", referencedColumnName="id")},
     * )
     */
    private $characteristics;

    /**
     * @Prezent\Translations(targetEntity="Orkestro\Bundle\ProductBundle\Entity\ProductKindTranslation")
     */
    protected $translations;

    /**
     * @Prezent\CurrentLocale
     */
    private $currentLocale;

    /**
     * @var ProductKindTranslation $currentTranslation
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
        $this->characteristics = new ArrayCollection();
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
            $translation = new ProductKindTranslation();
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
     * Add characteristics
     *
     * @param \Orkestro\Bundle\ProductBundle\Entity\Characteristic\Characteristic $characteristics
     * @return ProductKind
     */
    public function addCharacteristic(Characteristic $characteristics)
    {
        $this->characteristics[] = $characteristics;

        return $this;
    }

    /**
     * Remove characteristics
     *
     * @param \Orkestro\Bundle\ProductBundle\Entity\Characteristic\Characteristic $characteristics
     */
    public function removeCharacteristic(Characteristic $characteristics)
    {
        $this->characteristics->removeElement($characteristics);
    }

    /**
     * Get characteristics
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCharacteristics()
    {
        return $this->characteristics;
    }
}
