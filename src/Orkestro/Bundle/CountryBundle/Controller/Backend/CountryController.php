<?php

namespace Orkestro\Bundle\CountryBundle\Controller\Backend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orkestro\Bundle\CountryBundle\Entity\Country;
use Orkestro\Bundle\CountryBundle\Form\CountryType;

class CountryController extends Controller
{
    /**
     * Lists all Country entities.
     *
     * @Route("/list", name="orkestro_backend_country_list")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine.orm.default_entity_manager');
        $dql = 'SELECT c FROM OrkestroCountryBundle:Country c ORDER BY c.isoCode ASC';
        $query = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            10
        );

        return array(
            'pagination' => $pagination,
        );
    }

    /**
     * Creates a new Country entity.
     *
     * @Route("/", name="orkestro_backend_country_create")
     * @Method("POST")
     * @Template("OrkestroCountryBundle:Backend/Country:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Country();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('orkestro_backend_country_show', array('iso_code' => $entity->getIsoCode())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Country entity.
     *
     * @param Country $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Country $entity)
    {
        $form = $this->createForm(new CountryType($this->get('doctrine.orm.entity_manager')->getRepository('OrkestroLocaleBundle:Locale')), $entity, array(
            'action' => $this->generateUrl('orkestro_backend_country_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Country entity.
     *
     * @Route("/new", name="orkestro_backend_country_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Country();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Country entity.
     *
     * @Route("/{iso_code}", name="orkestro_backend_country_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($iso_code)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrkestroCountryBundle:Country')->findOneBy(array(
                'isoCode' => $iso_code,
            ));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Country entity.');
        }

        $deleteForm = $this->createDeleteForm($iso_code);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Country entity.
     *
     * @Route("/{iso_code}/edit", name="orkestro_backend_country_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($iso_code)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrkestroCountryBundle:Country')->findOneBy(array(
                'isoCode' => $iso_code,
            ));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Country entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($iso_code);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Country entity.
    *
    * @param Country $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Country $entity)
    {
        $form = $this->createForm(new CountryType($this->get('doctrine.orm.entity_manager')->getRepository('OrkestroLocaleBundle:Locale')), $entity, array(
            'action' => $this->generateUrl('orkestro_backend_country_update', array('iso_code' => $entity->getIsoCode())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Country entity.
     *
     * @Route("/{iso_code}", name="orkestro_backend_country_update")
     * @Method("PUT")
     * @Template("OrkestroCountryBundle:Backend/Country:edit.html.twig")
     */
    public function updateAction(Request $request, $iso_code)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrkestroCountryBundle:Country')->findOneBy(array(
                'isoCode' => $iso_code
            ));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Country entity.');
        }

        $deleteForm = $this->createDeleteForm($iso_code);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('orkestro_backend_country_edit', array('iso_code' => $iso_code)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    /**
     * Deletes a Country entity.
     *
     * @Route("/{iso_code}", name="orkestro_backend_country_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $iso_code)
    {
        $form = $this->createDeleteForm($iso_code);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OrkestroCountryBundle:Country')->findOneBy(array(
                    'isoCode' => $iso_code
                ));

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Country entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('orkestro_backend_country_list'));
    }

    /**
     * Creates a form to delete a Country entity by iso code.
     *
     * @param mixed $iso_code The entity iso code
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($iso_code)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('orkestro_backend_country_delete', array('iso_code' => $iso_code)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
