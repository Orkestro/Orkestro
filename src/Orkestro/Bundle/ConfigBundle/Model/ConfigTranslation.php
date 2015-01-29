<?php

namespace Orkestro\Bundle\ConfigBundle\Model;

use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Orkestro\Bundle\CoreBundle\Model\AbstractTranslation;

class ConfigTranslation extends AbstractTranslation
{
    /**
     * @Prezent\Translatable(targetEntity="Orkestro\Bundle\ConfigBundle\Model\Config")
     */
    protected $translatable;

    /**
     * Locale
     *
     * @Prezent\Locale
     */
    protected $locale;

    protected $test;

    public function getTest()
    {
        return $this->test;
    }

    public function setTest($test)
    {
        $this->test = $test;
        return $this;
    }
}