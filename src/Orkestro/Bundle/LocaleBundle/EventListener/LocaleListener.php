<?php

namespace Orkestro\Bundle\LocaleBundle\EventListener;

use Orkestro\Bundle\LocaleBundle\Model\Locale;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleListener implements EventSubscriberInterface
{
    private $defaultLocaleCode;
    private $availableLocales = [];

    public function __construct($defaultLocale, RegistryInterface $doctrine, ContainerInterface $container)
    {
        $locales = $doctrine
            ->getRepository('Orkestro\Bundle\LocaleBundle\Model\Locale')
            ->findBy(array(
                    'isEnabled' => true,
                ), array(
                    'isFallback' => 'DESC'
                ))
        ;

        /** @var Locale $locale */
        foreach ($locales as $locale) {
            if ($locale->getIsFallback()) {
                $this->defaultLocaleCode = $locale->getCode();
            }

            $this->availableLocales[] = $locale->getCode();
        }

        if (empty($this->defaultLocaleCode)) {
            $this->defaultLocaleCode = $defaultLocale;
        }
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        $locale = $request->attributes->get('_locale');

        if ($locale && in_array($locale, $this->availableLocales)) {
            $request->getSession()->set('_locale', $locale);
        } else {
            $sessionLocale = $request->getSession()->get('_locale', $this->defaultLocaleCode);
            if (in_array($sessionLocale, $this->availableLocales)) {
                $request->setLocale($sessionLocale);
            } else {
                $request->setLocale($this->defaultLocaleCode);
                $request->getSession()->set('_locale', $this->defaultLocaleCode);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(
                array(
                    'onKernelRequest',
                    17,
                ),
            ),
        );
    }
}