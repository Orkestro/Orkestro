<?php

namespace Orkestro\Bundle\CoreBundle;

use Orkestro\Bundle\CoreBundle\DependencyInjection\OrkestroCoreExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OrkestroCoreBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new OrkestroCoreExtension();
    }
}
