<?php

namespace Orkestro\Bundle\CountryBundle\Form;

use Orkestro\Bundle\CoreBundle\Form\AbstractTranslatableType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CountryType extends AbstractTranslatableType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', 'a2lix_translations', array(
                    'locales' => $this->getLocales(),
                    'default_locale' => 'en',
                    'fields' => array(
                        'title' => array(
                            'field_type' => 'text',
                            'locale_options' => $this->getTranslationsForFieldName('title', 'country', 'backend'),
                        ),
                        'metaTitle' => array(
                            'field_type' => 'textarea',
                            'locale_options' => $this->getTranslationsForFieldName('metaTitle', 'country', 'backend'),
                        ),
                        'metaDescription' => array(
                            'field_type' => 'textarea',
                            'locale_options' => $this->getTranslationsForFieldName('metaDescription', 'country', 'backend'),
                        ),
                        'metaKeywords' => array(
                            'field_type' => 'textarea',
                            'locale_options' => $this->getTranslationsForFieldName('metaKeywords', 'country', 'backend'),
                        ),
                    ),
                    'label' => false,
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
