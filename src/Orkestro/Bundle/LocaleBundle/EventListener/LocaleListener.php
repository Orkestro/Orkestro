<?php

namespace Orkestro\Bundle\LocaleBundle\EventListener;

use Orkestro\Bundle\LocaleBundle\Entity\Locale;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleListener implements EventSubscriberInterface
{
    private $defaultLocale;

    public function __construct(RegistryInterface $doctrine)
    {
        $locales = $doctrine->getRepository('OrkestroLocaleBundle:Locale')->findBy(array(
                'enabled' => true,
            ), array(
                'fallback' => 'DESC',
            ));

        /** @var Locale $locale */
        foreach ($locales as $locale) {
            if ($locale->getFallback()) {
                $this->defaultLocale = $locale->getCode();
                break;
            }
        }
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->hasPreviousSession()) {
            return;
        }

        if ($locale = $request->attributes->get('_locale')) {
            $request->getSession()->set('_locale', $locale);
        } else {
            $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(
                array(
                    'onKernelRequest',
                    17
                ),
            )
        );
    }
}