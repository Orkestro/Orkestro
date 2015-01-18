<?php

namespace Orkestro\Bundle\ManufacturerBundle\Form;

use Orkestro\Bundle\LocaleBundle\Entity\Locale;
use Orkestro\Bundle\LocaleBundle\Entity\LocaleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ManufacturerEnablerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enabled', 'checkbox', array(
                    'required' => false,
                    'label' => false,
                    'attr' => array(
                        'class' => 'switchery submittable',
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
        return 'orkestro_bundle_manufacturerbundle_manufacturer_enabler';
    }
}
