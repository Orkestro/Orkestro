<?php

namespace Orkestro\Bundle\CountryBundle\EventListener;

use Orkestro\Bundle\CoreBundle\EventListener\AbstractMenuListener;
use Orkestro\Bundle\WebBundle\Event\ConfigureMenuEvent;

class ConfigureMenuListener extends AbstractMenuListener
{
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();

        $menu->addChild('country', array(
                'route' => 'orkestro_backend_country',
            ))->setLabel($this->translate('orkestro.backend.country'));;
    }
}