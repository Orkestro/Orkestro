<?php

namespace Orkestro\Bundle\ProductBundle\Form;

use Orkestro\Bundle\CoreBundle\Form\AbstractTranslatableType;
use Orkestro\Bundle\ProductBundle\Form\Characteristic\CharacteristicSelectorType;
use Orkestro\Bundle\ProductBundle\Form\Characteristic\CharacteristicType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductKindType extends AbstractTranslatableType
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
            ->add('characteristics', 'collection', array(
                    'type' => new CharacteristicSelectorType($this->localeRepository),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orkestro\Bundle\ProductBundle\Entity\ProductKind'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orkestro_bundle_productbundle_productkind';
    }
}
