<?php

namespace Orkestro\Bundle\ProductBundle\Form\Characteristic;

use Orkestro\Bundle\CoreBundle\Form\AbstractTranslatableType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ValueType extends AbstractTranslatableType
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
                        'value' => array(
                            'field_type' => 'text',
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
            'data_class' => 'Orkestro\Bundle\ProductBundle\Entity\Characteristic\Value'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orkestro_bundle_productbundle_characteristic_value';
    }
}
