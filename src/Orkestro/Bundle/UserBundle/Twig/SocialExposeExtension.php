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
        return array(
            'socials' => $this->container->getParameter('hwi_oauth.resource_owners'),
        );
    }

    public function getName()
    {
        return 'social_expose_extension';
    }
}