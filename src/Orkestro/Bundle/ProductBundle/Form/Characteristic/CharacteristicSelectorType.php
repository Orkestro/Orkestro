<?php

namespace Orkestro\Bundle\ProductBundle\Form\Characteristic;

use Orkestro\Bundle\CoreBundle\Form\AbstractTranslatableType;
use Orkestro\Bundle\ProductBundle\Entity\Characteristic\Characteristic;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CharacteristicSelectorType extends AbstractTranslatableType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('characteristics', 'entity', array(
                    'class' => 'OrkestroProductBundle:Characteristic\Characteristic',
                ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orkestro\Bundle\ProductBundle\Entity\Characteristic\Characteristic'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orkestro_bundle_productbundle_characteristic_selector';
    }
}
