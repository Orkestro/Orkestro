<?php

namespace Orkestro\Bundle\LocaleBundle\EventListener;

use Orkestro\Bundle\CoreBundle\EventListener\AbstractMenuListener;
use Orkestro\Bundle\WebBundle\Event\ConfigureMenuEvent;

class ConfigureMenuListener extends AbstractMenuListener
{
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu('configuration');

        $menu->addChild('locale', array(
                'route' => 'orkestro_backend_locale_list',
            ))->setLabel($this->translate('orkestro.backend.locale'));;
    }
}