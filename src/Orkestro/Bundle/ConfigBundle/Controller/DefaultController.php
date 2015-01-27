<?php

namespace Orkestro\Bundle\ConfigBundle\Controller;

use Orkestro\Bundle\ConfigBundle\Model\Config;
use Orkestro\Bundle\ConfigBundle\Form\ConfigType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/hello", name="hello")
     * @Template()
     */
    public function indexAction()
    {
        $entity = new Config();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Country entity.
     *
     * @param Config $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Config $entity)
    {
        $form = $this->createForm(new ConfigType($this->getDoctrine()->getManager()->getRepository('OrkestroLocaleBundle:Locale'), $this->get('translator')), $entity, array(
                'action' => $this->generateUrl('hello_create'),
                'method' => 'POST',
            ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Creates a new Country entity.
     *
     * @Route("/", name="hello_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $entity = new Config();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('hello'));
        }

        return array();
    }
}
