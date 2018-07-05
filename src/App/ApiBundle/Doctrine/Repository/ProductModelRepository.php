<?php

namespace App\ApiBundle\Doctrine\Repository;

/**
 * Class ProductModelRepository.
 *
 * @package    App\ApiBundle\Doctrine\Repository
 * @author     Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class ProductModelRepository extends AbstractApiRepository
{
    /**
     * Retrieve product model owned by categories.
     * Many left join were done to check all given categories.
     *
     * @param array $categoryIds the category ids.
     *
     * @return array
     */
    public function findByAllCategoryIds(array $categoryIds): array
    {
        $queryBuilder = $this->getQueryBuilder('pm');

        foreach ($categoryIds as $id) {
            $queryBuilder->leftJoin('pm.categories', sprintf('categories%s', reset($id)));
        }

        foreach ($categoryIds as $id) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->in(sprintf('categories%s', reset($id)), $id)
            );
        }

        return $queryBuilder
            ->getQuery()
            ->execute();
    }
}