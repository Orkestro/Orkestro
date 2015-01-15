<?php

namespace Orkestro\Bundle\WebBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class PaginationLimitSelectorType extends AbstractType
{
    protected $translator;
    protected $selectedLimit;

    public function __construct(TranslatorInterface $translator, $limit)
    {
        $this->translator = $translator;
        $this->selectedLimit = $limit;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('limit', 'choice', array(
                    'choices' => array(
                        10 => $this->translator->trans('orkestro.info.limit_to', array('%limit%' => 10), 'backend'),
                        25 => $this->translator->trans('orkestro.info.limit_to', array('%limit%' => 25), 'backend'),
                        50 => $this->translator->trans('orkestro.info.limit_to', array('%limit%' => 50), 'backend'),
                        100 => $this->translator->trans('orkestro.info.limit_to', array('%limit%' => 100), 'backend'),
                    ),
                    'attr' => array(
                        'class' => 'selectpicker',
                    ),
                    'label' => false,
                    'data' => $this->selectedLimit,
                )
            )
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orkestro_bundle_webbundle_backend_pagination_limit_selector';
    }
}
