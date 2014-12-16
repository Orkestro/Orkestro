<?php

namespace Orkestro\Bundle\WebBundle\Menu;

use Orkestro\Bundle\WebBundle\Event\ConfigureMenuEvent;
use Symfony\Component\HttpFoundation\Request;

class BackendMenuBuilder extends AbstractMenuBuilder
{
    public function createMainMenu(Request $request)
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('dashboard', array(
                'route' => 'orkestro_backend_dashboard',
            ))->setLabel($this->translate('orkestro.backend.dashboard'));
        $menu->addChild('assortment');
        $menu->addChild('configuration');

        $this->eventDispatcher->dispatch(ConfigureMenuEvent::BACKEND_MAIN, new ConfigureMenuEvent($this->factory, $menu));

        return $menu;
    }
}