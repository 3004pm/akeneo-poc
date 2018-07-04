<?php

namespace App\ApiBundle\Controller\Gesco;

use App\ApiBundle\Doctrine\Repository\CategoryRepository;
use App\ApiBundle\Normalizer\Gesco\CategoryNormalizer;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CategoryController.
 *
 * @package    App\ApiBundle\Controller\Gesco
 * @author     Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
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
     * List all products.
     *
     * @param string $code   The root category code.
     * @param string $locale The locale of normalize label.
     *
     * @AclAncestor("pim_api_category_list")
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function listProductsAction(string $code, string $locale): JsonResponse
    {
        $category = $this->categoryRepository->findRootCategoryByCode($code);

        if (null === $category) {
            throw new NotFoundHttpException(sprintf('No root category found for code "%s".', $code));
        }

        return new JsonResponse(
            $this->categoryNormalizer->normalizeProductsData($category, 'gesco', ['locale' => $locale])
        );
    }
}
