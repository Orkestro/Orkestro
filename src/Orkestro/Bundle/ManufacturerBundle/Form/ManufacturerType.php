<?php

namespace Orkestro\Bundle\ManufacturerBundle\Form;

use Orkestro\Bundle\CoreBundle\Form\AbstractTranslatableType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ManufacturerType extends AbstractTranslatableType
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
                        ),
                        'shortDescription' => array(
                            'field_type' => 'textarea',
                        ),
                        'fullDescription' => array(
                            'field_type' => 'textarea',
                        ),
                    ),
                ))
            ->add('url')
            ->add('enabled', 'checkbox', array(
                    'required' => false,
                ))
            ->add('country', 'entity', array(
                    'class' => 'Orkestro\Bundle\CountryBundle\Entity\Country'
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
        return 'orkestro_bundle_manufacturerbundle_manufacturer';
    }
}
