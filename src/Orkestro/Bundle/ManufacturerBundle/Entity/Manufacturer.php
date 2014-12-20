<?php

namespace Orkestro\Bundle\ManufacturerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Orkestro\Bundle\CountryBundle\Entity\Country;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslatable;

/**
 * Manufacturer
 *
 * @ORM\Table(name="orkestro_manufacturer")
 * @ORM\Entity(repositoryClass="Orkestro\Bundle\ManufacturerBundle\Entity\ManufacturerRepository")
 */
class Manufacturer extends AbstractTranslatable
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
     * @Prezent\Translations(targetEntity="Orkestro\Bundle\ManufacturerBundle\Entity\ManufacturerTranslation")
     */
    protected $translations;

    /**
     * @Prezent\CurrentLocale
     */
    private $currentLocale;

    /**
     * @var ManufacturerTranslation $currentTranslation
     */
    private $currentTranslation;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string")
     */
    private $url;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @ORM\ManyToOne(targetEntity="Orkestro\Bundle\CountryBundle\Entity\Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     */
    private $country;


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
            $translation = new ManufacturerTranslation();
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Manufacturer
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set country
     *
     * @param Country $country
     * @return Manufacturer
     */
    public function setCountry(Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Manufacturer
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }
}
