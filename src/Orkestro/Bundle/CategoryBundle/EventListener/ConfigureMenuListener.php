<?php

namespace Orkestro\Bundle\CategoryBundle\EventListener;

use Orkestro\Bundle\CoreBundle\EventListener\AbstractMenuListener;
use Orkestro\Bundle\WebBundle\Event\ConfigureMenuEvent;

class ConfigureMenuListener extends AbstractMenuListener
{
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu('assortment');

        $menu->addChild('category', array(
                'route' => 'orkestro_backend_category_list',
            ))->setLabel($this->translate('orkestro.backend.category'));;
    }
}