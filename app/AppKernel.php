<?php

use Orkestro\Bundle\CoreBundle\Kernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // Add your bundles here
        );

        return array_merge(parent::registerBundles(), $bundles);
    }
}
