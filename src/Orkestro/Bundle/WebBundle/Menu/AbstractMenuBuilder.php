<?php

namespace Orkestro\Bundle\WebBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;

abstract class AbstractMenuBuilder extends ContainerAware
{
    protected $factory;
    protected $translator;
    protected $eventDispatcher;

    public function __construct(
        FactoryInterface $factory,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->factory = $factory;
        $this->translator = $translator;
        $this->eventDispatcher = $eventDispatcher;
    }

    protected function translate($label, $parameters = array())
    {
        return $this->translator->trans($label, $parameters, 'menu');
    }
}