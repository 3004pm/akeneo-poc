<?php

namespace App\ApiBundle\Normalizer\Gesco;

use Akeneo\Bundle\StorageUtilsBundle\Doctrine\Common\Detacher\ObjectDetacher;
use App\ApiBundle\Doctrine\Repository\CategoryRepository;
use App\ApiBundle\Doctrine\Repository\ProductModelRepository;
use App\ApiBundle\Doctrine\Repository\ProductRepository;
use Pim\Component\Catalog\Model\CategoryInterface;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Model\ProductModelInterface;

/**
 * Class CategoryNormalizer.
 *
 * @package    App\ApiBundle\Normalizer\Gesco
 * @author     Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class CategoryNormalizer
{
    private const CATEGORY_CODES_SEPARATOR = ',';

    /** @var CategoryRepository */
    protected $categoryRepository;
    /** @var ProductRepository */
    protected $productRepository;
    /** @var ProductModelRepository */
    protected $productModelRepository;
    /** @var ObjectDetacher */
    protected $objectDetacher;

    /**
     * CategoryController constructor.
     *
     * @param CategoryRepository     $categoryRepository
     * @param ProductRepository      $productRepository
     * @param ProductModelRepository $productModelRepository
     * @param ObjectDetacher         $objectDetacher
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        ProductModelRepository $productModelRepository,
        ObjectDetacher $objectDetacher
    ) {
        $this->categoryRepository     = $categoryRepository;
        $this->productRepository      = $productRepository;
        $this->productModelRepository = $productModelRepository;
        $this->objectDetacher         = $objectDetacher;
    }

    /**
     * Normalize product liste for gesco.
     *
     * @param CategoryInterface $category The category where product come from.
     * @param string|null       $format   The normalizer format.
     * @param array             $context  The context of normalization.
     *
     * @return array
     */
    public function normalizeProductsList(CategoryInterface $category, $format = null, array $context = []): array
    {
        $data = [];

        $categoryCodes = explode(self::CATEGORY_CODES_SEPARATOR, $context['categoryCodes']);

        /** @var CategoryInterface $childCategory */
        foreach ($category->getChildren() as $childCategory) {
            $allCategoryCodes = \array_merge($categoryCodes, [$childCategory->getCode()]);

            $data[$childCategory->getId()] = [
                'categoryCode'  => $childCategory->getCode(),
                'categoryLabel' => (string)$childCategory->setLocale($context['locale']),
                'products'      => array_merge(
                    $this->getNonVariantProductData($allCategoryCodes),
                    $this->getProductModelData($allCategoryCodes)
                ),
            ];
        }

        return $data;
    }

    /**
     * Retrieve non variant product data from the given category.
     *
     * @param array $categoryCodes The category codes owned by products.
     *
     * @return array
     */
    protected function getNonVariantProductData(array $categoryCodes): array
    {
        $categoryIds  = $this->categoryRepository->findIdsByCodes($categoryCodes);
        $products     = $this->productRepository->findNonVariantProductByAllCategories($categoryIds);
        $productsData = [];

        /** @var ProductInterface $product */
        foreach ($products as $product) {
            $productsData[$product->getId()] = [
                'isProductModel' => false,
                'identifier'     => $product->getIdentifier(),
            ];
        }

        $this->objectDetacher->detachAll($products);

        return $productsData;
    }

    /**
     * Retrieve product model data contains in all the given categories.
     *
     * @param array $categoryCodes
     *
     * @return array
     */
    protected function getProductModelData(array $categoryCodes): array
    {
        $categoryIds       = $this->categoryRepository->findIdsByCodes($categoryCodes);
        $productModels     = $this->productModelRepository->findByAllCategoryIds($categoryIds);
        $productModelsData = [];


        /** @var ProductModelInterface $productModel */
        foreach ($productModels as $productModel) {
            $productModelsData[$productModel->getId()] = [
                'isProductModel' => true,
                'identifier'     => $productModel->getCode(),
            ];
        }

        $this->objectDetacher->detachAll($productModels);

        return $productModelsData;
    }
}
