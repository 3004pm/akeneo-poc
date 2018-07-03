<?php

namespace App\ApiBundle\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class ProductModelRepository.
 *
 * @package    App\ApiBundle\Doctrine\Repository
 * @author     Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class ProductModelRepository
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
     * Retrieve variant product owned by category.
     *
     * @param int $categoryId
     *
     * @return array
     */
    public function findByCategory(int $categoryId): array
    {
        $queryBuilder = $this->getQueryBuilder();

        return $queryBuilder
            ->leftJoin('pm.categories', 'categories')
            ->where($queryBuilder->expr()->andX(
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
    protected function getQueryBuilder(string $alias = 'pm'): QueryBuilder
    {
        return $this->repository->createQueryBuilder($alias);
    }
}