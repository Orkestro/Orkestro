<?php

namespace Orkestro\Bundle\CoreBundle\Controller;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Form\FormTypeInterface;

interface AbstractBackendControllerInterface
{
    public function getModelRepository();
    public function getModelManager();

    public function getNamespace();
    public function getModelClass();
    public function getPaginationQuery();
    public function getIndexListForms(PaginationInterface $pagination);
}