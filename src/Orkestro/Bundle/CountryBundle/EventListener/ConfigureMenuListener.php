<?php

namespace Orkestro\Bundle\CountryBundle\EventListener;

use Orkestro\Bundle\CoreBundle\EventListener\AbstractMenuListener;
use Orkestro\Bundle\WebBundle\Event\ConfigureMenuEvent;

class ConfigureMenuListener extends AbstractMenuListener
{
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu('configuration');

        $menu
            ->addChild('country', array(
                'route' => 'orkestro_backend_country_list',
            ))
            ->setLabel($this->translate('orkestro.backend.country'))
            ->setCurrent(preg_match('/^orkestro_backend_country_/', $this->currentRoute))
            ->setChildrenAttributes(array(
                    'icon' => 'flag',
                ))
        ;
    }
}