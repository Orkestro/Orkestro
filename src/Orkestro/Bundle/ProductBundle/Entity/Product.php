<?php

namespace Orkestro\Bundle\ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslatable;

/**
 * Product
 *
 * @ORM\Table(name="orkestro_product")
 * @ORM\Entity(repositoryClass="Orkestro\Bundle\ProductBundle\Entity\ProductRepository")
 */
class Product extends AbstractTranslatable
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
     * @Prezent\Translations(targetEntity="Orkestro\Bundle\ProductBundle\Entity\ProductTranslation")
     */
    protected $translations;

    /**
     * @Prezent\CurrentLocale
     */
    private $currentLocale;

    /**
     * @var ProductTranslation $currentTranslation
     */
    private $currentTranslation;

    /**
     * @ORM\Column(name="sku", type="string", length=255)
     */
    private $sku;


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
            $translation = new ProductTranslation();
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

    public function getShortDescription()
    {
        return $this->translate()->getShortDescription();
    }

    public function setShortDescription($title)
    {
        $this->translate()->setShortDescription($title);
        return $this;
    }

    public function getFullDescription()
    {
        return $this->translate()->getFullDescription();
    }

    public function setFullDescription($title)
    {
        $this->translate()->setFullDescription($title);
        return $this;
    }

    /**
     * Set sku
     *
     * @param string $sku
     * @return Product
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get sku
     *
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }
}
