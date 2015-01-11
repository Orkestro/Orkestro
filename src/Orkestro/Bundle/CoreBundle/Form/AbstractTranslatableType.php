<?php

namespace Orkestro\Bundle\CoreBundle\Form;

use Orkestro\Bundle\LocaleBundle\Entity\Locale;
use Orkestro\Bundle\LocaleBundle\Entity\LocaleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Translation\TranslatorInterface;

abstract class AbstractTranslatableType extends AbstractType
{
    /** @var LocaleRepository $localeRepository */
    protected $localeRepository;
    /** @var TranslatorInterface $translator */
    protected $translator;

    public function __construct(LocaleRepository $localeRepository, TranslatorInterface $translator)
    {
        $this->localeRepository = $localeRepository;
        $this->translator = $translator;
    }

    protected function getLocales()
    {
        $existingLocales = $this->localeRepository->findBy(array(
                'enabled' => true,
            ));
        $locales = [];

        /** @var Locale $existingLocale */
        foreach ($existingLocales as $existingLocale) {
            $locales[] = $existingLocale->getCode();
        }

        return $locales;
    }

    /**
     * @return Locale
     */
    protected function getDefaultLocale()
    {
        return $this->localeRepository->findOneBy(array(
                'enabled' => true,
                'fallback' => true,
            ));
    }

    protected function getTranslationsForFieldName($fieldName, $entityName, $domain)
    {
        $locales = $this->getLocales();
        $defaultLocale = $this->getDefaultLocale();

        $translations = array();

        /** @var Locale $locale */
        foreach ($locales as $locale) {
            $translations[$locale] = array(
                'label' => $this->translator->trans(vsprintf('orkestro.%s.labels.%s', array(
                            $entityName,
                            $fieldName,
                        )), array(), $domain, $defaultLocale->getCode()),
            );
        }

        return $translations;
    }
}