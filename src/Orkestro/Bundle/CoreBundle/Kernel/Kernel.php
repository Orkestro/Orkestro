<?php

/*
 * This file is part of the Orkestro package.
 *
 * (c) Orkestro team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Orkestro\Bundle\CoreBundle\Kernel;

use Symfony;
use Doctrine;
use Sensio;
use Orkestro;
use Knp;
use FOS;
use HWI;
use Stof;
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
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),

            new Orkestro\Bundle\CoreBundle\OrkestroCoreBundle(),
            new Orkestro\Bundle\AddressingBundle\OrkestroAddressingBundle(),
            new Orkestro\Bundle\ApiBundle\OrkestroApiBundle(),
            new Orkestro\Bundle\CartBundle\OrkestroCartBundle(),
            new Orkestro\Bundle\CountryBundle\OrkestroCountryBundle(),
            new Orkestro\Bundle\DeliveryBundle\OrkestroDeliveryBundle(),
            new Orkestro\Bundle\LocaleBundle\OrkestroLocaleBundle(),
            new Orkestro\Bundle\ManufacturerBundle\OrkestroManufacturerBundle(),
            new Orkestro\Bundle\OrderBundle\OrkestroOrderBundle(),
            new Orkestro\Bundle\ProductBundle\OrkestroProductBundle(),
            new Orkestro\Bundle\UserBundle\OrkestroUserBundle(),
            new Orkestro\Bundle\WebBundle\OrkestroWebBundle(),
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