<?php

namespace Orkestro\Bundle\CountryBundle\Form;

use Orkestro\Bundle\LocaleBundle\Entity\Locale;
use Orkestro\Bundle\LocaleBundle\Entity\LocaleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CountryType extends AbstractType
{
    /** @var LocaleRepository $localeRepository */
    private $localeRepository;

    public function __construct(LocaleRepository $localeRepository)
    {
        $this->localeRepository = $localeRepository;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $existingLocales = $this->localeRepository->findBy([
                'enabled' => true,
            ]);
        $locales = [];

        /** @var Locale $existingLocale */
        foreach ($existingLocales as $existingLocale) {
            $locales[] = $existingLocale->getCode();
        }

        $builder
            ->add('translations', 'a2lix_translations_gedmo', array(
                    'translatable_class' => 'Orkestro\Bundle\CountryBundle\Entity\Country',
                    'locales' => $locales,
                ))
            ->add('isoCode')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orkestro\Bundle\CountryBundle\Entity\Country'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orkestro_bundle_countrybundle_country';
    }
}
