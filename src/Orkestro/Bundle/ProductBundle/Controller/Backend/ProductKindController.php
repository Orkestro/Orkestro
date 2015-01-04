<?php

namespace Orkestro\Bundle\ProductBundle\Controller\Backend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orkestro\Bundle\ProductBundle\Entity\ProductKind;
use Orkestro\Bundle\ProductBundle\Form\ProductKindType;

class ProductKindController extends Controller
{

    /**
     * Lists all ProductKind entities.
     *
     * @Route("/list", name="orkestro_backend_product_kind_list")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('OrkestroProductBundle:ProductKind')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new ProductKind entity.
     *
     * @Route("/", name="orkestro_backend_product_kind_create")
     * @Method("POST")
     * @Template("OrkestroProductBundle:ProductKind:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new ProductKind();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('orkestro_backend_product_kind_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a ProductKind entity.
     *
     * @param ProductKind $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ProductKind $entity)
    {
        $form = $this->createForm(new ProductKindType($this->get('doctrine.orm.entity_manager')->getRepository('OrkestroLocaleBundle:Locale')), $entity, array(
            'action' => $this->generateUrl('orkestro_backend_product_kind_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new ProductKind entity.
     *
     * @Route("/new", name="orkestro_backend_product_kind_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new ProductKind();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a ProductKind entity.
     *
     * @Route("/{id}", name="orkestro_backend_product_kind_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrkestroProductBundle:ProductKind')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductKind entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ProductKind entity.
     *
     * @Route("/{id}/edit", name="orkestro_backend_product_kind_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrkestroProductBundle:ProductKind')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductKind entity.');
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
    * Creates a form to edit a ProductKind entity.
    *
    * @param ProductKind $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ProductKind $entity)
    {
        $form = $this->createForm(new ProductKindType($this->get('doctrine.orm.entity_manager')->getRepository('OrkestroLocaleBundle:Locale')), $entity, array(
            'action' => $this->generateUrl('orkestro_backend_product_kind_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing ProductKind entity.
     *
     * @Route("/{id}", name="orkestro_backend_product_kind_update")
     * @Method("PUT")
     * @Template("OrkestroProductBundle:ProductKind:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrkestroProductBundle:ProductKind')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductKind entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('orkestro_backend_product_kind_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a ProductKind entity.
     *
     * @Route("/{id}", name="orkestro_backend_product_kind_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OrkestroProductBundle:ProductKind')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ProductKind entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('orkestro_backend_product_kind'));
    }

    /**
     * Creates a form to delete a ProductKind entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('orkestro_backend_product_kind_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
