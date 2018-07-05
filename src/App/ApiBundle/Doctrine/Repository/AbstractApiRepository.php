<?php

namespace App\ApiBundle\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

abstract class AbstractApiRepository
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
     * Retrieve query builder from entity repository.
     *
     * @param string $alias The table alias.
     *
     * @return QueryBuilder
     */
    protected function getQueryBuilder(string $alias = null): QueryBuilder
    {
        return $this->repository->createQueryBuilder($alias);
    }
}
