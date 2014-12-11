<?php

namespace Orkestro\Bundle\WebBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DashboardController extends Controller
{
    /**
     * @Route("/dashboard", name="orkestro_backend_dashboard")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
}
