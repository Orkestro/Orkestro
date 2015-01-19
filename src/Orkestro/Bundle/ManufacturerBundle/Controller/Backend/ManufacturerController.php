<?php

namespace Orkestro\Bundle\ManufacturerBundle\Controller\Backend;

use Orkestro\Bundle\ManufacturerBundle\Entity\ManufacturerRepository;
use Orkestro\Bundle\ManufacturerBundle\Form\ManufacturerEnablerType;
use Orkestro\Bundle\ManufacturerBundle\Form\ManufacturerPresenterType;
use Orkestro\Bundle\WebBundle\Form\Backend\PaginationLimitSelectorType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orkestro\Bundle\ManufacturerBundle\Entity\Manufacturer;
use Orkestro\Bundle\ManufacturerBundle\Form\ManufacturerType;

class ManufacturerController extends Controller
{

    /**
     * Lists all Manufacturer entities.
     *
     * @Route("/list", name="orkestro_backend_manufacturer_list")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $listLimit = $request->getSession()->get('orkestro_backend_country_list_limit', 25);

        $em = $this->getDoctrine()->getManager();

        /** @var ManufacturerRepository $repository */
        $repository = $em->getRepository('OrkestroManufacturerBundle:Manufacturer');
        $queryBuilder = $repository->createQueryBuilder('m');
        $queryBuilder
            ->select('m', 'mt', 'c', 'ct')
            ->add('from', 'OrkestroManufacturerBundle:Manufacturer m JOIN m.translations mt JOIN m.country c JOIN c.translations ct WITH mt.locale = :locale AND ct.locale = :locale')
            ->groupBy('m')
            ->setParameters(array(
                    ':locale' => $request->getLocale(),
                ))
        ;

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->get('page', 1),
            $listLimit
        );

        $forms = array();

        /** @var Manufacturer $manufacturer */
        foreach ($pagination as $manufacturer) {
            $forms[$manufacturer->getId()]['enable'] = $this->createEnableForm($manufacturer)->createView();
            $forms[$manufacturer->getId()]['delete'] = $this->createDeleteForm($manufacturer->getId())->createView();
        }

        $formLimitSelector = $this->createLimitSelectorForm($listLimit)->createView();

        return array(
            'pagination' => $pagination,
            'forms' => $forms,
            'formLimitSelector' => $formLimitSelector,
        );
    }

    /**
     * @Route("/limit", name="orkestro_backend_manufacturer_limit")
     * @Method("PUT")
     */
    public function limitAction(Request $request)
    {
        $listLimit = $request->getSession()->get('orkestro_backend_manufacturer_list_limit', 25);

        $form = $this->createLimitSelectorForm($listLimit);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $formData = $form->getData();

            $request->getSession()->set('orkestro_backend_manufacturer_list_limit', $formData['limit']);
        }

        return $this->redirect($this->generateUrl('orkestro_backend_manufacturer_list'));
    }

    private function createLimitSelectorForm($selectedLimit)
    {
        $form = $this->createForm(new PaginationLimitSelectorType($this->get('translator'), $selectedLimit), null, array(
                'action' => $this->generateUrl('orkestro_backend_manufacturer_limit'),
                'method' => 'PUT',
            ));

        return $form;
    }

    /**
     * @Route("/enable/{id}", name="orkestro_backend_manufacturer_enable")
     * @Method("PUT")
     */
    public function enableAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrkestroManufacturerBundle:Manufacturer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Manufacturer entity.');
        }

        $enableForm = $this->createEnableForm($entity);
        $enableForm->handleRequest($request);

        if ($enableForm->isValid()) {
            $em->flush();
        }

        return $this->redirect($this->generateUrl('orkestro_backend_manufacturer_list'));
    }

    private function createEnableForm(Manufacturer $entity)
    {
        $form = $this->createForm(new ManufacturerEnablerType(), $entity, array(
                'action' => $this->generateUrl('orkestro_backend_manufacturer_enable', array('id' => $entity->getId())),
                'method' => 'PUT',
            ));

        return $form;
    }

    /**
     * Creates a new Manufacturer entity.
     *
     * @Route("/", name="orkestro_backend_manufacturer_create")
     * @Method("POST")
     * @Template("OrkestroManufacturerBundle:Backend/Manufacturer:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Manufacturer();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('orkestro_backend_manufacturer_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Manufacturer entity.
     *
     * @param Manufacturer $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Manufacturer $entity)
    {
        $form = $this->createForm(new ManufacturerType($this->get('doctrine.orm.entity_manager')->getRepository('OrkestroLocaleBundle:Locale'), $this->get('translator')), $entity, array(
            'action' => $this->generateUrl('orkestro_backend_manufacturer_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Manufacturer entity.
     *
     * @Route("/new", name="orkestro_backend_manufacturer_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Manufacturer();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Manufacturer entity.
     *
     * @Route("/{id}", name="orkestro_backend_manufacturer_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrkestroManufacturerBundle:Manufacturer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Manufacturer entity.');
        }

        $translationForm = $this->createShowTranslationForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'translation_form' => $translationForm->createView(),
        );
    }

    /**
     * Creates a form to show a Manufacturer translations.
     *
     * @param Manufacturer $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createShowTranslationForm(Manufacturer $entity)
    {
        $form = $this->createForm(new ManufacturerPresenterType($this->getDoctrine()->getManager()->getRepository('OrkestroLocaleBundle:Locale'), $this->get('translator')), $entity);

        return $form;
    }

    /**
     * Displays a form to edit an existing Manufacturer entity.
     *
     * @Route("/{id}/edit", name="orkestro_backend_manufacturer_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrkestroManufacturerBundle:Manufacturer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Manufacturer entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Manufacturer entity.
    *
    * @param Manufacturer $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Manufacturer $entity)
    {
        $form = $this->createForm(new ManufacturerType($this->get('doctrine.orm.entity_manager')->getRepository('OrkestroLocaleBundle:Locale'), $this->get('translator')), $entity, array(
            'action' => $this->generateUrl('orkestro_backend_manufacturer_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Manufacturer entity.
     *
     * @Route("/{id}", name="orkestro_backend_manufacturer_update")
     * @Method("PUT")
     * @Template("OrkestroManufacturerBundle:Backend/Manufacturer:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrkestroManufacturerBundle:Manufacturer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Manufacturer entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('orkestro_backend_manufacturer_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Manufacturer entity.
     *
     * @Route("/{id}", name="orkestro_backend_manufacturer_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OrkestroManufacturerBundle:Manufacturer')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Manufacturer entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('orkestro_backend_manufacturer_list'));
    }

    /**
     * Creates a form to delete a Manufacturer entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('orkestro_backend_manufacturer_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
