<?php

namespace Orkestro\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslation;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractSeoTaggableTranslation extends AbstractTranslation
{
    /**
     * @ORM\Column(name="meta_title", type="text")
     */
    protected $metaTitle;

    /**
     * @ORM\Column(name="meta_description", type="text")
     */
    protected $metaDescription;

    /**
     * @ORM\Column(name="meta_keywords", type="text")
     */
    protected $metaKeywords;
}
