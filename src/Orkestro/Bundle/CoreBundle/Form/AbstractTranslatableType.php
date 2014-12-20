<?php

namespace Orkestro\Bundle\CoreBundle\Form;

use Orkestro\Bundle\LocaleBundle\Entity\Locale;
use Orkestro\Bundle\LocaleBundle\Entity\LocaleRepository;
use Symfony\Component\Form\AbstractType;

abstract class AbstractTranslatableType extends AbstractType
{
    /** @var LocaleRepository $localeRepository */
    private $localeRepository;

    public function __construct(LocaleRepository $localeRepository)
    {
        $this->localeRepository = $localeRepository;
    }

    protected function getLocales()
    {
        $existingLocales = $this->localeRepository->findBy([
                'enabled' => true,
            ]);
        $locales = [];

        /** @var Locale $existingLocale */
        foreach ($existingLocales as $existingLocale) {
            $locales[] = $existingLocale->getCode();
        }

        return $locales;
    }
}