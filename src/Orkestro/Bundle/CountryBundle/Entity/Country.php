<?php

namespace Orkestro\Bundle\CountryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

/**
 * Country
 *
 * @ORM\Table(name="orkestro_country")
 * @ORM\Entity(repositoryClass="Orkestro\Bundle\CountryBundle\Entity\CountryRepository")
 */
class Country
{
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=128)
     * @Gedmo\Translatable
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="iso_code", type="string", length=2)
     * @ORM\Id
     */
    private $isoCode;

    /**
     * @Gedmo\Locale
     */
    private $locale;


    /**
     * Set title
     *
     * @param string $title
     * @return Country
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set isoCode
     *
     * @param string $isoCode
     * @return Country
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    /**
     * Get isoCode
     *
     * @return string 
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }
}
