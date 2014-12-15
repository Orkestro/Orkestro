<?php

namespace Orkestro\Bundle\UserBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class SocialExposeExtension extends \Twig_Extension
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getGlobals()
    {
        $availabeSocials = $this->container->getParameter('hwi_oauth.resource_owners');

        foreach ($availabeSocials as $socialKey => $social) {
            $appId = $this->container->getParameter(sprintf('orkestro.oauth.%s.app_id', $social));

            if (empty($appId) || 'none' == $appId) {
                unset($availabeSocials[$socialKey]);
            }
        }

        return array(
            'availableSocials' => $availabeSocials,
        );
    }

    public function getName()
    {
        return 'social_expose_extension';
    }
}