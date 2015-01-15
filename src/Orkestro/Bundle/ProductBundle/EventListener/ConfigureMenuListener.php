<?php

namespace Orkestro\Bundle\ProductBundle\EventListener;

use Orkestro\Bundle\CoreBundle\EventListener\AbstractMenuListener;
use Orkestro\Bundle\WebBundle\Event\ConfigureMenuEvent;

class ConfigureMenuListener extends AbstractMenuListener
{
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu('assortment');

        $productMenu = $menu
            ->addChild('product_group')
            ->setLabel($this->translate('orkestro.backend.product'))
            ->setCurrent(preg_match('/^orkestro_backend_product_/', $this->currentRoute))
            ->setChildrenAttributes(array(
                    'icon' => 'cubes',
                ))
        ;

        $productMenu
            ->addChild('product', array(
                'route' => 'orkestro_backend_product_list',
            ))
            ->setLabel($this->translate('orkestro.backend.product'))
            ->setCurrent(preg_match('/^orkestro_backend_product_/', $this->currentRoute))
        ;

        $productMenu
            ->addChild('product_kind', array(
                'route' => 'orkestro_backend_product_kind_list',
            ))
            ->setLabel($this->translate('orkestro.backend.product_kind'))
            ->setCurrent(preg_match('/^orkestro_backend_product_kind_/', $this->currentRoute))
        ;

        $productMenu
            ->addChild('product_characteristic', array(
                'route' => 'orkestro_backend_product_characteristic_list',
            ))
            ->setLabel($this->translate('orkestro.backend.product_characteristic'))
            ->setCurrent(preg_match('/^orkestro_backend_product_characteristic_/', $this->currentRoute))
        ;
    }
}