<?php

namespace Orkestro\Bundle\WebBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\Event;

class ConfigureMenuEvent extends Event
{
    const BACKEND_MAIN = 'orkestro.menu_builder.backend.main.configure';
    const FRONTEND_MAIN = 'orkestro.menu_builder.frontend.main.configure';

    private $factory;
    private $menu;

    public function __construct(FactoryInterface $factory, ItemInterface $menu)
    {
        $this->factory = $factory;
        $this->menu = $menu;
    }

    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return \Knp\Menu\ItemInterface
     */
    public function getMenu()
    {
        return $this->menu;
    }
}