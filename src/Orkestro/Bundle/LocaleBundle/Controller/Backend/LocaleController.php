<?php

namespace Orkestro\Bundle\LocaleBundle\Controller\Backend;

use Doctrine\Common\Persistence\ObjectRepository;
use Orkestro\Bundle\CoreBundle\Controller\AbstractBackendController;
use Orkestro\Bundle\LocaleBundle\Form\LocaleEnablerType;
use Orkestro\Bundle\LocaleBundle\Form\LocaleFallbackerType;
use Orkestro\Bundle\LocaleBundle\Model\Locale;
use Orkestro\Bundle\WebBundle\Form\Backend\PaginationLimitSelectorType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orkestro\Bundle\LocaleBundle\Form\LocaleType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Intl;

class LocaleController extends AbstractBackendController
{
    /**
     * @Route("/set/{_locale}", name="orkestro_backend_locale_set")
     * @Method("POST")
     */
    public function setLocaleAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            return new Response(json_encode(array(
                        'status' => 0,
                    )));
        }

        throw $this->createNotFoundException();
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
        $listLimit = $request->getSession()->get('orkestro_backend_locale_list_limit', 25);

        $em = $this->getDoctrine()->getManager();
        /** @var ObjectRepository $repository */
        $repository = $em->getRepository('Orkestro\Bundle\LocaleBundle\Model\Locale');
        $queryBuilder = $repository->createQueryBuilder('l');

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->get('page', 1),
            $listLimit
        );

        $forms = array();

        /** @var Locale $locale */
        foreach ($pagination as $locale) {
            $forms[$locale->getCode()]['enable'] = $this->createEnableForm($locale)->createView();
            $forms[$locale->getCode()]['fallback'] = $this->createFallbackForm($locale)->createView();
            $forms[$locale->getCode()]['delete'] = $this->createDeleteForm($locale->getCode())->createView();
        }

        $formLimitSelector = $this->createLimitSelectorForm($listLimit)->createView();

        return array(
            'pagination' => $pagination,
            'forms' => $forms,
            'formLimitSelector' => $formLimitSelector,
        );
    }

    /**
     * @Route("/limit", name="orkestro_backend_locale_limit")
     * @Method("PUT")
     */
    public function limitAction(Request $request)
    {
        $listLimit = $request->getSession()->get('orkestro_backend_locale_list_limit', 25);

        $form = $this->createLimitSelectorForm($listLimit);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $formData = $form->getData();

            $request->getSession()->set('orkestro_backend_locale_list_limit', $formData['limit']);
        }

        return $this->redirect($this->generateUrl('orkestro_backend_locale_list'));
    }

    private function createLimitSelectorForm($selectedLimit)
    {
        $form = $this->createForm(new PaginationLimitSelectorType($this->get('translator'), $selectedLimit), null, array(
                'action' => $this->generateUrl('orkestro_backend_locale_limit'),
                'method' => 'PUT',
            ));

        return $form;
    }

    /**
     * @Route("/enable/{code}", name="orkestro_backend_locale_enable")
     * @Method("PUT")
     */
    public function enableAction(Request $request, $code)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('Orkestro\Bundle\LocaleBundle\Model\Locale')->find($code);

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

        /** @var ObjectRepository $localeRepository */
        $localeRepository = $em->getRepository('Orkestro\Bundle\LocaleBundle\Model\Locale');

        /** @var Locale $entity */
        $entity = $localeRepository->find($code);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Locale entity.');
        }

        $enableForm = $this->createFallbackForm($entity);
        $enableForm->handleRequest($request);

        if ($enableForm->isValid()) {
            if ($entity->getIsFallback() && $entity->getIsEnabled()) {
                $locales = $localeRepository->findBy(array(
                        'isFallback' => true,
                    ));

                /** @var Locale $locale */
                foreach ($locales as $locale) {
                    if ($locale != $entity) {
                        $locale->setIsFallback(false);
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

            if ($entity->getIsFallback()) {
                if ($entity->getIsEnabled()) {
                    /** @var ObjectRepository $localeRepository */
                    $localeRepository = $em->getRepository('Orkestro\Bundle\LocaleBundle\Model\Locale');

                    $locales = $localeRepository->findBy(
                        array(
                            'isFallback' => true,
                        )
                    );

                    /** @var Locale $locale */
                    foreach ($locales as $locale) {
                        $locale->setIsFallback(false);
                    }
                } else {
                    $request->getSession()->getFlashBag()->add(
                        'danger',
                        $this->get('translator')->trans('orkestro.locale.notifications.add_fallback_enabled_problem', array(
                                '%locale_name%' => $entity->getTitle(),
                            ), 'backend')
                    );
                    return $this->redirect($this->generateUrl('orkestro_backend_locale_list'));
                }
            }

            $em->persist($entity);
            $em->flush();

            $request->getSession()->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('orkestro.locale.notifications.add_success', array(
                        '%locale_name%' => $entity->getTitle(),
                    ), 'backend')
            );

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
        $form = $this->createForm(new LocaleType($this->getDoctrine()->getManager()->getRepository('Orkestro\Bundle\LocaleBundle\Model\Locale')), $entity, array(
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

        $entity = $em->getRepository('Orkestro\Bundle\LocaleBundle\Model\Locale')->find($code);

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

        $entity = $em->getRepository('Orkestro\Bundle\LocaleBundle\Model\Locale')->find($code);

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

        /** @var Locale $entity */
        $entity = $em->getRepository('Orkestro\Bundle\LocaleBundle\Model\Locale')->find($code);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Locale entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $request->getSession()->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('orkestro.locale.notifications.edit_success', array(
                        '%locale_name%' => $entity->getTitle(),
                    ), 'backend')
            );

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
            $entity = $em->getRepository('Orkestro\Bundle\LocaleBundle\Model\Locale')->find($code);

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
