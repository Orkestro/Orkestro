<?php

namespace Orkestro\Bundle\ManufacturerBundle\Form;

use Orkestro\Bundle\CoreBundle\Form\AbstractTranslatableType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ManufacturerPresenterType extends AbstractTranslatableType
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
                    'fields' => array(
                        'title' => array(
                            'field_type' => 'text',
                            'locale_options' => $this->getTranslationsForFieldName('title', 'manufacturer', 'backend'),
                        ),
                        'shortDescription' => array(
                            'field_type' => 'textarea',
                            'locale_options' => $this->getTranslationsForFieldName('title', 'shortDescription', 'backend'),
                        ),
                        'fullDescription' => array(
                            'field_type' => 'textarea',
                            'locale_options' => $this->getTranslationsForFieldName('title', 'fullDescription', 'backend'),
                        ),
                        'metaTitle' => array(
                            'field_type' => 'textarea',
                            'locale_options' => $this->getTranslationsForFieldName('metaTitle', 'manufacturer', 'backend'),
                        ),
                        'metaDescription' => array(
                            'field_type' => 'textarea',
                            'locale_options' => $this->getTranslationsForFieldName('metaDescription', 'manufacturer', 'backend'),
                        ),
                        'metaKeywords' => array(
                            'field_type' => 'textarea',
                            'locale_options' => $this->getTranslationsForFieldName('metaKeywords', 'manufacturer', 'backend'),
                        ),
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
            'data_class' => 'Orkestro\Bundle\ManufacturerBundle\Entity\Manufacturer'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orkestro_bundle_manufacturerbundle_manufacturer_presenter';
    }
}
