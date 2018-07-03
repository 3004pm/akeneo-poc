<?php

namespace App\ApiBundle\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class ProductRepository.
 *
 * @package    App\ApiBundle\Doctrine\Repository
 * @author     Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class ProductRepository
{
    /** @var EntityRepository */
    protected $repository;

    /**
     * EntityRepository constructor.
     *
     * @param EntityRepository $repository
     */
    public function __construct(EntityRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retrieve non variant product owned by category.
     *
     * @param int $categoryId the category id.
     *
     * @return array
     */
    public function findNonVariantProductByCategory(int $categoryId): array
    {
        $queryBuilder = $this->getQueryBuilder();

        return $queryBuilder
            ->leftJoin('p.categories', 'categories')
            ->where($queryBuilder->expr()->andX(
                $queryBuilder->expr()->isNull('p.familyVariant'),
                $queryBuilder->expr()->eq('categories', ':categoryId')
            ))
            ->setParameter('categoryId', $categoryId)
            ->getQuery()
            ->execute();
    }

    /**
     * @param string $alias The table alias.
     *
     * @return QueryBuilder
     */
    protected function getQueryBuilder(string $alias = 'p'): QueryBuilder
    {
        return $this->repository->createQueryBuilder($alias);
    }
}