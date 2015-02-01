<?php

namespace Orkestro\Bundle\LocaleBundle\Controller\Backend;

use Doctrine\Common\Persistence\ObjectRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Orkestro\Bundle\CoreBundle\Controller\AbstractBackendController;
use Orkestro\Bundle\LocaleBundle\Form\LocaleEnablerType;
use Orkestro\Bundle\LocaleBundle\Form\LocaleFallbackerType;
use Orkestro\Bundle\LocaleBundle\Model\Locale;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orkestro\Bundle\LocaleBundle\Form\LocaleType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Intl;

class LocaleController extends AbstractBackendController
{
    public function getNamespace()
    {
        return 'orkestro_backend_locale';
    }

    public function getModelClass()
    {
        return 'Orkestro\Bundle\LocaleBundle\Model\Locale';
    }

    public function getIndexListForms(PaginationInterface $pagination)
    {
        $forms = array();

        /** @var Locale $locale */
        foreach ($pagination as $locale) {
            $forms[$locale->getCode()]['enable'] = $this->createEnableForm($locale)->createView();
            $forms[$locale->getCode()]['fallback'] = $this->createFallbackForm($locale)->createView();
            $forms[$locale->getCode()]['delete'] = $this->createDeleteForm(
                $this->generateUrl('orkestro_backend_locale_delete', array('code' => $locale->getCode()))
            )->createView();
        }

        return $forms;
    }

    /**
     * @Route("/limit", name="orkestro_backend_locale_limit")
     * @Method("PUT")
     */
    public function limitAction(Request $request)
    {
        return parent::limitAction($request);
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
        return parent::indexAction($request);
    }

    /**
     * @Route("/set/{_locale}", name="orkestro_backend_locale_set")
     * @Method("POST")
     */
    public function setLocaleAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            return new Response(
                json_encode(
                    array(
                        'status' => 0,
                    )
                )
            );
        }

        throw $this->createNotFoundException();
    }

    /**
     * @Route("/enable/{code}", name="orkestro_backend_locale_enable")
     * @Method("PUT")
     */
    public function enableAction(Request $request, $code)
    {
        $entity = $this->getModelRepository()->find($code);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Locale entity.');
        }

        $enableForm = $this->createEnableForm($entity);
        $enableForm->handleRequest($request);

        if ($enableForm->isValid()) {
            $this->getModelManager()->flush();
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
        /** @var ObjectRepository $localeRepository */
        $localeRepository = $this->getModelRepository();

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

            $this->getModelManager()->flush();
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
        $form = $this->createCreateForm(
            new LocaleType($this->getModelRepository()),
            $entity
        );
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getModelManager();
            $entity->setTitle(Intl::getLocaleBundle()->getLocaleName($entity->getCode(), $entity->getCode()));

            if ($entity->getIsFallback()) {
                if ($entity->getIsEnabled()) {
                    /** @var ObjectRepository $localeRepository */
                    $localeRepository = $this->getModelRepository();

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
     * Displays a form to create a new Locale entity.
     *
     * @Route("/new", name="orkestro_backend_locale_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Locale();
        $form   = $this->createCreateForm(
            new LocaleType($this->getModelRepository()),
            $entity
        );

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
        $entity = $this->getModelRepository()->find($code);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Locale entity.');
        }

        $deleteForm = $this->createDeleteForm(
            $this->generateUrl('orkestro_backend_locale_delete', array('code' => $code))
        );

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
        $entity = $this->getModelRepository()->find($code);

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
        /** @var Locale $entity */
        $entity = $this->getModelRepository()->find($code);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Locale entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if ($entity->getIsFallback()) {
                if ($entity->getIsEnabled()) {
                    $localeRepository = $this->getModelRepository();

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
                        $this->get('translator')->trans('orkestro.locale.notifications.edit_fallback_enabled_problem', array(
                                '%locale_name%' => $entity->getTitle(),
                            ), 'backend')
                    );
                    return $this->redirect($this->generateUrl('orkestro_backend_locale_edit', array('code' => $code)));
                }
            }

            $this->getModelManager()->flush();

            $request->getSession()->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('orkestro.locale.notifications.edit_success', array(
                        '%locale_name%' => $entity->getTitle(),
                    ), 'backend')
            );

            return $this->redirect($this->generateUrl('orkestro_backend_locale_list'));
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
        $form = $this->createDeleteForm(
            $this->generateUrl('orkestro_backend_locale_delete', array('code' => $code))
        );
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getModelManager();
            $entity = $this->getModelRepository()->find($code);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Locale entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('orkestro_backend_locale_list'));
    }
}
