<?php

namespace Orkestro\Bundle\CountryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

/**
 * @ORM\Table(name="country_translation",
 *      uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *          "locale",
 *          "object_id",
 *          "field"
 *      })}
 * )
 * @ORM\Entity(repositoryClass="Gedmo\Translatable\Entity\Repository\TranslationRepository")
 */
class CountryTranslation extends AbstractPersonalTranslation
{
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="Orkestro\Bundle\CountryBundle\Entity\Country", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="iso_code", onDelete="CASCADE")
     */
    protected $object;
}
