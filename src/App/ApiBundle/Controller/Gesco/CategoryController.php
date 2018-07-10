<?php

namespace App\ApiBundle\Controller\Gesco;

use App\ApiBundle\Doctrine\Repository\CategoryRepository;
use App\ApiBundle\Helper\CategoryHelper;
use App\ApiBundle\Helper\LocaleHelper;
use App\ApiBundle\Normalizer\Gesco\CategoryNormalizer;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CategoryController.
 *
 * @package App\ApiBundle\Controller\Gesco
 * @author  Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class CategoryController
{
    /** @var CategoryRepository */
    protected $categoryRepository;
    /** @var CategoryNormalizer */
    protected $categoryNormalizer;

    /**
     * CategoryController constructor.
     *
     * @param CategoryRepository $categoryRepository
     * @param CategoryNormalizer $categoryNormalizer
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        CategoryNormalizer $categoryNormalizer
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->categoryNormalizer = $categoryNormalizer;
    }

    /**
     * List all products of vertical category and in categories field in parameters.
     *
     * @param string $categoryCodes he category codes where the product have to be.
     *
     * @AclAncestor("pim_api_category_list")
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function listProductsAction(string $categoryCodes): JsonResponse
    {
        $category = $this->categoryRepository->findRootCategoryByCode(CategoryHelper::CATEGORY_LIST_PRODUCTS);

        if (null === $category) {
            throw new NotFoundHttpException(
                sprintf('No root category found for code "%s".', CategoryHelper::CATEGORY_LIST_PRODUCTS)
            );
        }

        return new JsonResponse(
            $this->categoryNormalizer->normalizeProductsList(
                $category,
                'gesco',
                ['locale' => LocaleHelper::DEFAULT_LOCALE, 'categoryCodes' => $categoryCodes]
            )
        );
    }
}
