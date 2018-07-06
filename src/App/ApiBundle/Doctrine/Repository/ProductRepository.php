<?php

namespace App\ApiBundle\Doctrine\Repository;

/**
 * Class ProductRepository.
 *
 * @package    App\ApiBundle\Doctrine\Repository
 * @author     Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class ProductRepository extends AbstractApiRepository
{
    /**
     * Retrieve non variant product owned by categories.
     * Many left join were done to check all given categories.
     *
     * @param array $categoryIds The category ids.
     *
     * @return array
     */
    public function findNonVariantProductByAllCategories(array $categoryIds): array
    {
        $queryBuilder = $this->getQueryBuilder('p');

        foreach ($categoryIds as $id) {
            $queryBuilder->leftJoin('p.categories', sprintf('categories%s', reset($id)));
        }

        foreach ($categoryIds as $id) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->in(sprintf('categories%s', reset($id)), $id)
            );
        }

        return $queryBuilder
            ->andWhere($queryBuilder->expr()->isNull('p.familyVariant'))
            ->getQuery()
            ->execute();
    }
}
