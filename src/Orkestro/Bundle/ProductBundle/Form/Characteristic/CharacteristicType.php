<?php

namespace Orkestro\Bundle\ProductBundle\Form\Characteristic;

use Orkestro\Bundle\CoreBundle\Form\AbstractTranslatableType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CharacteristicType extends AbstractTranslatableType
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
                    ),
                ))
            ->add('type', 'choice', array(
                    'choices' => array(
                        0 => 'Boolean',
                        1 => 'Number',
                        2 => 'String',
                        3 => 'Text',
                    ),
                ))
            ->add('selectPolicy', 'choice', array(
                    'choices' => array(
                        0 => 'One',
                        1 => 'Many',
                    ),
                ))
            ->add('values', 'collection', array(
                    'type' => new ValueType($this->localeRepository),
                    'allow_add' => true,
                    'allow_delete' => true,
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
        return 'orkestro_bundle_productbundle_characteristic_characteristic';
    }
}
