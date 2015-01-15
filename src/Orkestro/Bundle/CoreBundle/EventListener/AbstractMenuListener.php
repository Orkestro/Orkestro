<?php

namespace Orkestro\Bundle\CoreBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

abstract class AbstractMenuListener
{
    protected $translator;
    protected $request;

    protected $currentRoute;

    public function __construct(TranslatorInterface $translator, Request $request)
    {
        $this->translator = $translator;
        $this->request = $request;

        $this->currentRoute = $request->get('_route');
    }

    /**
     * @param string $label
     */
    protected function translate($label, $parameters = array())
    {
        return $this->translator->trans($label, $parameters, 'menu');
    }
}