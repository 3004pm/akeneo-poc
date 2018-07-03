<?php

namespace App\ApiBundle\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Pim\Component\Catalog\Model\CategoryInterface;

/**
 * Class CategoryRepository.
 *
 * @package    App\ApiBundle\Doctrine\Repository
 * @author     Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class CategoryRepository
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
     * Retrieve cetegories by their parent's code.
     *
     * @param string $code The parent's category code.
     *
     * @return CategoryInterface[]|array
     */
    public function findByParentCode(string $code): ?array
    {
        $queryBuilder = $this->getQueryBuilder();

        return $queryBuilder
            ->leftJoin('c.parent', 'parent')
            ->where($queryBuilder->expr()->eq('parent.code', ':parentCode'))
            ->setParameter('parentCode', $code)
            ->getQuery()
            ->execute();
    }

    /**
     * Retrieve root category by code.
     *
     * @param string $code
     *
     * @return null|CategoryInterface
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findRootCategoryByCode(string $code): ?CategoryInterface
    {
        $queryBuilder = $this->getQueryBuilder();

        return $queryBuilder
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('c.code', ':code'),
                    $queryBuilder->expr()->isNull('c.parent')
                )
            )
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $alias The table alias.
     *
     * @return QueryBuilder
     */
    protected function getQueryBuilder(string $alias = 'c'): QueryBuilder
    {
        return $this->repository->createQueryBuilder($alias);
    }
}