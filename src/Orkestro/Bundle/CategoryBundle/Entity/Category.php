<?php

namespace Orkestro\Bundle\CategoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslatable;

/**
 * Category
 *
 * @ORM\Table(name="orkestro_category")
 * @ORM\Entity(repositoryClass="Orkestro\Bundle\CategoryBundle\Entity\CategoryRepository")
 */
class Category extends AbstractTranslatable
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
     * @Prezent\Translations(targetEntity="Orkestro\Bundle\CategoryBundle\Entity\CategoryTranslation")
     */
    protected $translations;

    /**
     * @Prezent\CurrentLocale
     */
    private $currentLocale;

    /**
     * @var CategoryTranslation $currentTranslation
     */
    private $currentTranslation;


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
            $translation = new CategoryTranslation();
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

    public function getUrl()
    {
        return $this->translate()->getUrl();
    }

    public function setUrl($url)
    {
        $this->translate()->setUrl($url);
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
}
