<?php

/*
 * This file is part of the Orkestro package.
 *
 * (c) Orkestro team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Orkestro\CoreBundle\Kernel;

use Symfony, Doctrine, Sensio, Orkestro;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

abstract class Kernel extends BaseKernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            new Orkestro\AddressingBundle\OrkestroAddressingBundle(),
            new Orkestro\CountryBundle\OrkestroCountryBundle(),
            new Orkestro\DeliveryBundle\OrkestroDeliveryBundle(),
            new Orkestro\ManufacturerBundle\OrkestroManufacturerBundle(),
            new Orkestro\OrderBundle\OrkestroOrderBundle(),
            new Orkestro\CartBundle\OrkestroCartBundle(),
            new Orkestro\ProductBundle\OrkestroProductBundle(),
            new Orkestro\UserBundle\OrkestroUserBundle(),
            new Orkestro\CoreBundle\OrkestroCoreBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $rootDir = $this->getRootDir();

        $loader->load($rootDir.'/config/config_'.$this->getEnvironment().'.yml');
    }
}