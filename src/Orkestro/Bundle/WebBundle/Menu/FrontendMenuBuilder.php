<?php

namespace Orkestro\Bundle\WebBundle\Menu;

use Orkestro\Bundle\WebBundle\Event\ConfigureMenuEvent;
use Symfony\Component\HttpFoundation\Request;

class FrontendMenuBuilder extends AbstractMenuBuilder
{
    public function createMainMenu(Request $request)
    {
        $menu = $this->factory->createItem('root');

        $this->eventDispatcher->dispatch(ConfigureMenuEvent::FRONTEND_MAIN, new ConfigureMenuEvent($this->factory, $menu));

        return $menu;
    }
}