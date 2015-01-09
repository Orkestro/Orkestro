<?php

namespace Orkestro\Bundle\LocaleBundle\Controller\Backend;

use Orkestro\Bundle\LocaleBundle\Entity\LocaleRepository;
use Orkestro\Bundle\LocaleBundle\Form\LocaleEnablerType;
use Orkestro\Bundle\LocaleBundle\Form\LocaleFallbackerType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orkestro\Bundle\LocaleBundle\Entity\Locale;
use Orkestro\Bundle\LocaleBundle\Form\LocaleType;
use Symfony\Component\Intl\Intl;

class LocaleController extends Controller
{
    /**
     * @Route("/set/{_locale}", name="orkestro_backend_locale_set")
     * @Method("GET")
     */
    public function setLocaleAction()
    {
        return $this->redirect($this->generateUrl('orkestro_backend_dashboard'), 302);
    }

    /**
     * Lists all Locale entities.
     *
     * @Route("/list", name="orkestro_backend_locale_list")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine.orm.default_entity_manager');
        $dql = 'SELECT l FROM OrkestroLocaleBundle:Locale l';
        $query = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            10
        );

        $forms = array();

        /** @var Locale $locale */
        foreach ($pagination as $locale) {
            $forms[$locale->getCode()]['enable'] = $this->createEnableForm($locale)->createView();
            $forms[$locale->getCode()]['fallback'] = $this->createFallbackForm($locale)->createView();
            $forms[$locale->getCode()]['delete'] = $this->createDeleteForm($locale->getCode())->createView();
        }

        return array(
            'pagination' => $pagination,
            'forms' => $forms,
        );
    }

    /**
     * @Route("/enable/{code}", name="orkestro_backend_locale_enable")
     * @Method("PUT")
     */
    public function enableAction(Request $request, $code)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrkestroLocaleBundle:Locale')->find($code);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Locale entity.');
        }

        $enableForm = $this->createEnableForm($entity);
        $enableForm->handleRequest($request);

        if ($enableForm->isValid()) {
            $em->flush();
        }

        return $this->redirect($this->generateUrl('orkestro_backend_locale_list'));
    }

    private function createEnableForm(Locale $entity)
    {
        $form = $this->createForm(new LocaleEnablerType(), $entity, array(
                'action' => $this->generateUrl('orkestro_backend_locale_enable', array('code' => $entity->getCode())),
                'method' => 'PUT',
            ));

        return $form;
    }

    /**
     * @Route("/fallback/{code}", name="orkestro_backend_locale_fallback")
     * @Method("PUT")
     */
    public function fallbackAction(Request $request, $code)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var LocaleRepository $localeRepository */
        $localeRepository = $em->getRepository('OrkestroLocaleBundle:Locale');

        /** @var Locale $entity */
        $entity = $localeRepository->find($code);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Locale entity.');
        }

        $enableForm = $this->createFallbackForm($entity);
        $enableForm->handleRequest($request);

        if ($enableForm->isValid()) {
            if ($entity->getFallback() && $entity->getEnabled()) {
                $locales = $localeRepository->findBy(array(
                        'fallback' => true,
                    ));

                /** @var Locale $locale */
                foreach ($locales as $locale) {
                    if ($locale != $entity) {
                        $locale->setFallback(false);
                    }
                }
            } else {
                return $this->redirect($this->generateUrl('orkestro_backend_locale_list'));
            }

            $em->flush();
        }

        return $this->redirect($this->generateUrl('orkestro_backend_locale_list'));
    }

    private function createFallbackForm(Locale $entity)
    {
        $form = $this->createForm(new LocaleFallbackerType(), $entity, array(
                'action' => $this->generateUrl('orkestro_backend_locale_fallback', array('code' => $entity->getCode())),
                'method' => 'PUT',
            ));

        return $form;
    }

    /**
     * Creates a new Locale entity.
     *
     * @Route("/", name="orkestro_backend_locale_create")
     * @Method("POST")
     * @Template("OrkestroLocaleBundle:Backend/Locale:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Locale();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setTitle(Intl::getLocaleBundle()->getLocaleName($entity->getCode(), $entity->getCode()));

            if ($entity->getFallback() && $entity->getEnabled()) {
                /** @var LocaleRepository $localeRepository */
                $localeRepository = $em->getRepository('OrkestroLocaleBundle:Locale');

                $locales = $localeRepository->findBy(array(
                        'fallback' => true,
                    ));

                /** @var Locale $locale */
                foreach ($locales as $locale) {
                    $locale->setFallback(false);
                }
            } else {
                return $this->redirect($this->generateUrl('orkestro_backend_locale_list'));
            }

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('orkestro_backend_locale_show', array('code' => $entity->getCode())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Locale entity.
     *
     * @param Locale $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Locale $entity)
    {
        $form = $this->createForm(new LocaleType($this->get('doctrine.orm.entity_manager')->getRepository('OrkestroLocaleBundle:Locale')), $entity, array(
            'action' => $this->generateUrl('orkestro_backend_locale_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Locale entity.
     *
     * @Route("/new", name="orkestro_backend_locale_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Locale();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Locale entity.
     *
     * @Route("/{code}", name="orkestro_backend_locale_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($code)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrkestroLocaleBundle:Locale')->find($code);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Locale entity.');
        }

        $deleteForm = $this->createDeleteForm($code);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Locale entity.
     *
     * @Route("/{code}/edit", name="orkestro_backend_locale_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($code)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrkestroLocaleBundle:Locale')->find($code);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Locale entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Locale entity.
    *
    * @param Locale $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Locale $entity)
    {
        $form = $this->createForm(new LocaleType(), $entity, array(
            'action' => $this->generateUrl('orkestro_backend_locale_update', array('code' => $entity->getCode())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Locale entity.
     *
     * @Route("/{code}", name="orkestro_backend_locale_update")
     * @Method("PUT")
     * @Template("OrkestroLocaleBundle:Backend/Locale:edit.html.twig")
     */
    public function updateAction(Request $request, $code)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrkestroLocaleBundle:Locale')->find($code);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Locale entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('orkestro_backend_locale_edit', array('code' => $code)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Deletes a Locale entity.
     *
     * @Route("/{code}", name="orkestro_backend_locale_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $code)
    {
        $form = $this->createDeleteForm($code);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OrkestroLocaleBundle:Locale')->find($code);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Locale entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('orkestro_backend_locale_list'));
    }

    /**
     * Creates a form to delete a Locale entity by code.
     *
     * @param mixed $code The entity code
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($code)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('orkestro_backend_locale_delete', array('code' => $code)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
