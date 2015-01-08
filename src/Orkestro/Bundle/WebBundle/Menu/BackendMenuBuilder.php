<?php

namespace Orkestro\Bundle\WebBundle\Menu;

use Orkestro\Bundle\WebBundle\Event\ConfigureMenuEvent;
use Symfony\Component\HttpFoundation\Request;

class BackendMenuBuilder extends AbstractMenuBuilder
{
    public function createMainMenu(Request $request)
    {
        $this->currentRoute = $request->get('_route');

        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes(array(
                'id' => 'mainnav-menu',
                'class' => 'list-group',
            ))
        ;

        $menu
            ->addChild('dashboard', array(
                'route' => 'orkestro_backend_dashboard',
            ))
            ->setLabel($this->translate('orkestro.backend.dashboard'))
            ->setCurrent('orkestro_backend_dashboard' == $this->currentRoute)
            ->setChildrenAttributes(array(
                    'icon' => 'tachometer',
                ))
        ;

        $menu
            ->addChild('assortment')
            ->setLabel($this->translate('orkestro.backend.headers.assortment'))
            ->setLinkAttributes(array(
                    'type' => 'header',
                ))
        ;
        $menu
            ->addChild('configuration')
            ->setLabel($this->translate('orkestro.backend.headers.configuration'))
            ->setLinkAttributes(array(
                    'type' => 'header',
                ))
        ;

        $this->eventDispatcher->dispatch(ConfigureMenuEvent::BACKEND_MAIN, new ConfigureMenuEvent($this->factory, $menu));

        return $menu;
    }
}