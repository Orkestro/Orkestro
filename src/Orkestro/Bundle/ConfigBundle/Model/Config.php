<?php

namespace Orkestro\Bundle\ConfigBundle\Model;

use Orkestro\Bundle\CoreBundle\Model\AbstractTranslatable;
use Prezent\Doctrine\Translatable\Annotation as Prezent;

class Config extends AbstractTranslatable
{
    /**
     * @Prezent\Translations(targetEntity="Orkestro\Bundle\ConfigBundle\Model\ConfigTranslation")
     */
    protected $translations;

    public function getTest()
    {
        return $this->translate()->getTest();
    }

    public function setTest($test)
    {
        $this->translate()->setTest($test);
        return $this;
    }
}
