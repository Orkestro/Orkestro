<?php

namespace Orkestro\Bundle\LocaleBundle\Form;

use Doctrine\Common\Persistence\ObjectRepository;
use Orkestro\Bundle\LocaleBundle\Model\Locale;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LocaleType extends AbstractType
{
    private $localeRepository;

    public function __construct(ObjectRepository $localeRepository = null)
    {
        $this->localeRepository = $localeRepository;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!empty($this->localeRepository)) {
            $locales = Intl::getLocaleBundle()->getLocaleNames();

            $existingLocales = $this->localeRepository->findAll();

            /** @var Locale $existingLocale */
            foreach ($existingLocales as $existingLocale) {
                unset($locales[$existingLocale->getCode()]);
            }

            $builder
                ->add('code', 'choice', array(
                        'choices' => $locales,
                        'attr' => array(
                            'class' => 'chosen',
                        ),
                    ))
            ;
        }

        $builder
            ->add('isEnabled', 'checkbox', array(
                    'required' => false,
                    'attr' => array(
                        'class' => 'switchery',
                    ),
                ))
            ->add('isFallback', 'checkbox', array(
                    'required' => false,
                    'attr' => array(
                        'class' => 'switchery',
                    ),
                ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orkestro\Bundle\LocaleBundle\Model\Locale'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orkestro_bundle_localebundle_locale';
    }
}
