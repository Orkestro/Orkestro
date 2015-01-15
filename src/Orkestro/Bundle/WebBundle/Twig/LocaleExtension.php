<?php

namespace Orkestro\Bundle\WebBundle\Twig;

use Orkestro\Bundle\LocaleBundle\Entity\Locale;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class LocaleExtension extends \Twig_Extension
{
    protected $localeRepository;
    protected $currentLocale;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->localeRepository = $doctrine->getRepository('OrkestroLocaleBundle:Locale');
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST == $event->getRequestType()) {
            $this->currentLocale = $event->getRequest()->getLocale();
        }
    }

    public function getGlobals()
    {
        $locales = array(
            'current' => null,
            'other' => null,
        );

        $enabledLocales = $this->localeRepository->findBy(array(
                'enabled' => true,
            ));

        /** @var Locale $locale */
        foreach ($enabledLocales as $localeKey => $locale) {
            if ($this->currentLocale == $locale->getCode()) {
                $locales['current'] = $locale;
                unset($enabledLocales[$localeKey]);
                break;
            }
        }

        $locales['other'] = $enabledLocales;

        return array(
            'locales' => $locales,
        );
    }

    public function getName()
    {
        return 'locale_extension';
    }
}
