<?php

namespace Orkestro\Bundle\WebBundle\Twig;

use Orkestro\Bundle\LocaleBundle\Model\Locale;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Intl\Intl;

class LocaleExtension extends \Twig_Extension
{
    protected $localeRepository;
    protected $currentLocale;
    protected $container;

    public function __construct(RegistryInterface $doctrine, ContainerInterface $container)
    {
        $this->localeRepository = $doctrine->getRepository('Orkestro\Bundle\LocaleBundle\Model\Locale');
        $this->container = $container;
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
                'isEnabled' => true,
            ));
        $otherLocales = $enabledLocales;

        if (count($enabledLocales)) {
            /** @var Locale $locale */
            foreach ($enabledLocales as $localeKey => $locale) {
                if ($this->currentLocale == $locale->getCode()) {
                    $locales['current'] = $locale;
                    unset($otherLocales[$localeKey]);
                    break;
                }
            }
        }
        if (!count($enabledLocales) || empty($locales['current'])) {
            $defaultLocaleCode = $this->container->getParameter('locale');

            $locale = new Locale();
            $locale->setCode($defaultLocaleCode);
            $locale->setTitle(Intl::getLocaleBundle()->getLocaleName($defaultLocaleCode, $defaultLocaleCode));
            $locales['current'] = $locale;
        }

        $locales['other'] = $otherLocales;

        return array(
            'locales' => $locales,
        );
    }

    public function getName()
    {
        return 'locale_extension';
    }
}
