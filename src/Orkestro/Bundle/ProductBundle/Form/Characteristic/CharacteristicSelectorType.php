<?php

namespace Orkestro\Bundle\ProductBundle\Form\Characteristic;

use Orkestro\Bundle\CoreBundle\Form\AbstractTranslatableType;
use Orkestro\Bundle\LocaleBundle\Entity\LocaleRepository;
use Orkestro\Bundle\ProductBundle\Entity\Characteristic\Characteristic;
use Orkestro\Bundle\ProductBundle\Entity\Characteristic\CharacteristicRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CharacteristicSelectorType extends AbstractTranslatableType
{
    protected $characteristicRepository;

    public function __construct(LocaleRepository $localeRepository, CharacteristicRepository $characteristicRepository)
    {
        parent::__construct($localeRepository);
        $this->characteristicRepository = $characteristicRepository;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $characteristics = $this->characteristicRepository->findAll();

        $choices = array();
        /** @var Characteristic $characteristic */
        foreach ($characteristics as $characteristic) {
            $choices[$characteristic->getId()] = $characteristic;
        }

        $resolver->setDefaults(array(
                'choices' => $choices,
                'class' => 'Orkestro\Bundle\ProductBundle\Entity\Characteristic\Characteristic',
            ));
    }

    public function getParent()
    {
        return 'entity';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orkestro_bundle_productbundle_characteristic_selector';
    }
}
