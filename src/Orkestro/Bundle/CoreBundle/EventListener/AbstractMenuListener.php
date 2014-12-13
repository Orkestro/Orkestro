<?php

namespace Orkestro\Bundle\CoreBundle\EventListener;

use Symfony\Component\Translation\TranslatorInterface;

abstract class AbstractMenuListener
{
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param string $label
     */
    protected function translate($label, $parameters = array())
    {
        return $this->translator->trans($label, $parameters, 'menu');
    }
}