<?php

namespace Orkestro\Bundle\CountryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Country
 *
 * @ORM\Table(name="orkestro_country")
 * @ORM\Entity(repositoryClass="Orkestro\Bundle\CountryBundle\Entity\CountryRepository")
 * @Gedmo\TranslationEntity(class="Orkestro\Bundle\CountryBundle\Entity\CountryTranslation")
 */
class Country
{
    /**
     * @var string
     *
     * @ORM\Column(name="iso_code", type="string", length=2)
     * @ORM\Id
     */
    private $isoCode;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=128)
     * @Gedmo\Translatable
     */
    private $title;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *   targetEntity="Orkestro\Bundle\CountryBundle\Entity\CountryTranslation",
     *   mappedBy="object",
     *   cascade={"persist", "remove"}
     * )
     */
    private $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Set translations
     *
     * @param ArrayCollection $translations
     * @return Country
     */
    public function setTranslations($translations)
    {
        foreach ($translations as $translation) {
            $translation->setObject($this);
        }

        $this->translations = $translations;

        return $this;
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
}
