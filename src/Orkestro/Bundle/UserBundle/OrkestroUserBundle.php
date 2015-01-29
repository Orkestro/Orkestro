<?php

namespace Orkestro\Bundle\UserBundle;

use Orkestro\Bundle\CoreBundle\AbstractOrkestroBundle;

class OrkestroUserBundle extends AbstractOrkestroBundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
