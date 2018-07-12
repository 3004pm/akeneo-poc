<?php

namespace App\CustomEntityBundle\Manager;

use App\CustomEntityBundle\Entity\DependentAttribute;
use Pim\Bundle\CustomEntityBundle\Manager\Manager;

/**
 * Class DependentAttributeManager.
 *
 * @package App\CustomEntityBundle\Manager
 * @author  Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class DependentAttributeManager extends Manager
{
    /**
     * Set dependent attribute code
     * with attribute code, option code, dependent attribute code and dependent option code concatenation.
     *
     * @param DependentAttribute $dependentAttribute The dependent attribute.
     */
    public function setCode(DependentAttribute $dependentAttribute): void
    {
        $dependentAttributeCode = sprintf(
            '%s_%s_%s_%s',
            trim($dependentAttribute->getAttributeCode()),
            trim($dependentAttribute->getOptionCode()),
            trim($dependentAttribute->getDependentAttributeCode()),
            trim($dependentAttribute->getDependentOptionCode())
        );

        $dependentAttribute->setCode($dependentAttributeCode);
    }

    /**
     * Retrive dependent attributs by criteria
     *
     * @param DependentAttribute $dependentAttribute The dependent attribute.
     * @param array              $criteria
     * @param array|null         $orderBy
     * @param null               $limit
     * @param null               $offset
     *
     * @return array
     */
    public function findBy(
        DependentAttribute $dependentAttribute,
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ): array {
        return $this->em->getRepository($dependentAttribute)->findBy($criteria, $orderBy, $limit, $offset);
    }
}