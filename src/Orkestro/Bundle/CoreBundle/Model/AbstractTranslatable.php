<?php

namespace Orkestro\Bundle\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Prezent\Doctrine\Translatable\TranslatableInterface;
use Prezent\Doctrine\Translatable\TranslationInterface;

abstract class AbstractTranslatable implements TranslatableInterface
{
    /**
     * @var integer
     */
    protected $id;

    private $currentLocale;

    /**
     * @var AbstractTranslation
     */
    private $currentTranslation;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the translations
     *
     * @return ArrayCollection
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Add a translation
     *
     * @param TranslationInterface $translation
     * @return self
     */
    public function addTranslation(TranslationInterface $translation)
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[] = $translation;
            $translation->setTranslatable($this);
        }

        return $this;
    }

    /**
     * Remove a translation
     *
     * @param TranslationInterface $translation
     * @return self
     */
    public function removeTranslation(TranslationInterface $translation)
    {
        if ($this->translations->removeElement($translation)) {
            $translation->setTranslatable(null);
        }

        return $this;
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
            $translationObjectClassName = get_class($this).'Translation';
            $translation = new $translationObjectClassName();
            $translation->setLocale($locale);
            $this->addTranslation($translation);
        }

        $this->currentTranslation = $translation;
        return $translation;
    }
}