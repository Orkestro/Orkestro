<?php

namespace Orkestro\Bundle\CoreBundle\Controller;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Orkestro\Bundle\WebBundle\Form\Backend\PaginationLimitSelectorType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractBackendController extends Controller implements AbstractBackendControllerInterface
{
    public function getModelManager()
    {
        return $this->getDoctrine()->getManager();
    }

    public function getModelRepository()
    {
        return $this->getModelManager()->getRepository($this->getModelClass());
    }

    public function getPaginationQuery()
    {
        return $this->getModelRepository()->createQueryBuilder('m');
    }

    public function getIndexListForms(PaginationInterface $pagination)
    {
        return array();
    }

    protected function createLimitSelectorForm($selectedLimit)
    {
        $form = $this->createForm(new PaginationLimitSelectorType($this->get('translator'), $selectedLimit), null, array(
                'action' => $this->generateUrl($this->getNamespace().'_limit'),
                'method' => 'PUT',
            ));

        return $form;
    }

    protected function limitAction(Request $request)
    {
        $listLimit = $request->getSession()->get($this->getNamespace().'_list_limit', 25);

        $form = $this->createLimitSelectorForm($listLimit);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $formData = $form->getData();

            $request->getSession()->set($this->getNamespace().'_list_limit', $formData['limit']);
        }

        return $this->redirect($this->generateUrl($this->getNamespace().'_list'));
    }

    protected function indexAction(Request $request)
    {
        $listLimit = $request->getSession()->get($this->getNamespace().'_list_limit', 25);

        $queryBuilder = $this->getPaginationQuery();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->get('page', 1),
            $listLimit
        );

        $forms = $this->getIndexListForms($pagination);

        $formLimitSelector = $this->createLimitSelectorForm($listLimit)->createView();

        return array(
            'pagination' => $pagination,
            'forms' => $forms,
            'formLimitSelector' => $formLimitSelector,
        );
    }

    protected function createCreateForm($type, $model)
    {
        $form = $this->createForm($type, $model, array(
                'action' => $this->generateUrl($this->getNamespace().'_create'),
                'method' => 'POST',
            ));

        $form->add('submit', 'submit');

        return $form;
    }

    protected function createDeleteForm($action)
    {
        return $this->createFormBuilder()
            ->setAction($action)
            ->setMethod('DELETE')
            ->add('submit', 'submit')
            ->getForm()
        ;
    }
}