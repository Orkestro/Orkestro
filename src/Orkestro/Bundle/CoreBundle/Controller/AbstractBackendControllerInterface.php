<?php

namespace Orkestro\Bundle\CoreBundle\Controller;

use Knp\Component\Pager\Pagination\PaginationInterface;

interface AbstractBackendControllerInterface
{
    public function getNamespace();
    public function getModelClass();
    public function getPaginationQuery();
    public function getIndexListForms(PaginationInterface $pagination);

    public function getModelRepository();
    public function getModelManager();
}