<?php

namespace Orkestro\Bundle\ManufacturerBundle\EventListener;

use Orkestro\Bundle\CoreBundle\EventListener\AbstractMenuListener;
use Orkestro\Bundle\WebBundle\Event\ConfigureMenuEvent;

class ConfigureMenuListener extends AbstractMenuListener
{
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu('assortment');

        $menu->addChild('manufacturer', array(
                'route' => 'orkestro_backend_manufacturer_list',
            ))->setLabel($this->translate('orkestro.backend.manufacturer'));;
    }
}