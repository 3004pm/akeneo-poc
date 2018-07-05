<?php

namespace App\ApiBundle\Doctrine\Repository;

use Pim\Component\Catalog\Model\CategoryInterface;

/**
 * Class CategoryRepository.
 *
 * @package    App\ApiBundle\Doctrine\Repository
 * @author     Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class CategoryRepository extends AbstractApiRepository
{
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
        $queryBuilder = $this->getQueryBuilder('c');

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
     * findIdsByCodes
     *
     * @param array $codes
     *
     * @return array
     */
    public function findIdsByCodes(array $codes): array
    {
        $queryBuilder = $this->getQueryBuilder('c');

        return $queryBuilder
            ->select('c.id')
            ->where(
                $queryBuilder->expr()->in('c.code', ':codes')
            )
            ->setParameter('codes', $codes)
            ->getQuery()
            ->execute();
    }
}
