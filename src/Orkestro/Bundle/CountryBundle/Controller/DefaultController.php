<?php

namespace Orkestro\Bundle\CountryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/country", name="orkestro_backend_country")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
}
