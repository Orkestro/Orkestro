<?php

namespace Orkestro\Bundle\WebBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class HomepageController extends Controller
{
    /**
     * @Route("/", name="orkestro_frontend_homepage")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
}
