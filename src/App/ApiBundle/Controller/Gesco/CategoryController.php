<?php

namespace App\ApiBundle\Controller\Gesco;

use App\ApiBundle\Doctrine\Repository\CategoryRepository;
use App\ApiBundle\Helper\CategoryHelper;
use App\ApiBundle\Helper\LocaleHelper;
use App\ApiBundle\Normalizer\Gesco\CategoryNormalizer;
use App\CacheBundle\Controller\AbstractController;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CategoryController.
 *
 * @package App\ApiBundle\Controller\Gesco
 * @author  Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class CategoryController extends AbstractController
{
    /** @var CategoryRepository */
    protected $categoryRepository;
    /** @var CategoryNormalizer */
    protected $categoryNormalizer;

    /**
     * List all products of vertical category and in categories field in parameters.
     *
     * @param Request $request       The request variable.
     * @param string  $categoryCodes he category codes where the product have to be.
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @AclAncestor("pim_api_category_list")
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function listProductsAction(Request $request, string $categoryCodes): JsonResponse
    {
        $cacheItem = $this->getCachedItem($request);

        if (false === $cacheItem->isHit()) {
            $category = $this->getCategoryRepository()->findRootCategoryByCode(CategoryHelper::CATEGORY_LIST_PRODUCTS);

            if (null === $category) {
                throw new NotFoundHttpException(
                    sprintf('No root category found for code "%s".', CategoryHelper::CATEGORY_LIST_PRODUCTS)
                );
            }

            $this->getRedisManager()->setCacheData(
                $cacheItem,
                $this->getCategoryNormalizer()->normalizeProductsList(
                    $category,
                    'gesco',
                    ['locale' => LocaleHelper::DEFAULT_LOCALE, 'categoryCodes' => $categoryCodes]
                )
            );
        }

        return new JsonResponse($this->getRedisManager()->getCacheData($cacheItem));
    }

    /**
     * Retrieve category repository.
     *
     * @return CategoryRepository
     */
    protected function getCategoryRepository(): CategoryRepository
    {
        if (null === $this->categoryRepository) {
            $this->categoryRepository = $this->getService('app_api.repository.category');
        }

        return $this->categoryRepository;
    }

    /**
     * Retrieve category normalizer.
     *
     * @return CategoryNormalizer
     */
    protected function getCategoryNormalizer(): CategoryNormalizer
    {
        if (null === $this->categoryNormalizer) {
            $this->categoryNormalizer = $this->getService('app_api.normalizer.gesco.category');
        }

        return $this->categoryNormalizer;
    }
}
