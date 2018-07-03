<?php

namespace App\ApiBundle\Normalizer\Gesco;

use Akeneo\Bundle\StorageUtilsBundle\Doctrine\Common\Detacher\ObjectDetacher;
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
    /** @var ProductRepository */
    protected $productRepository;
    /** @var ProductModelRepository */
    protected $productModelRepository;
    /** @var ObjectDetacher */
    protected $objectDetacher;

    /**
     * CategoryController constructor.
     *
     * @param ProductRepository      $productRepository
     * @param ProductModelRepository $productModelRepository
     * @param ObjectDetacher         $objectDetacher
     */
    public function __construct(
        ProductRepository $productRepository,
        ProductModelRepository $productModelRepository,
        ObjectDetacher $objectDetacher
    ) {
        $this->productRepository      = $productRepository;
        $this->productModelRepository = $productModelRepository;
        $this->objectDetacher         = $objectDetacher;
    }

    /**
     * Normalize product data from root category.
     *
     * @param CategoryInterface $category
     * @param null              $format
     * @param array             $context
     *
     * @return array
     */
    public function normalizeProductsData($category, $format = null, array $context = []): array
    {
        $data = [];

        /** @var CategoryInterface $childCategory */
        foreach ($category->getChildren() as $childCategory) {
            $data[$childCategory->getId()] = [
                'categoryCode'  => $childCategory->getCode(),
                'categoryLabel' => (string)$childCategory->setLocale($context['locale']),
                'products'      => array_merge(
                    $this->getNonVariantProductData($childCategory),
                    $this->getProductModelData($childCategory)
                ),
            ];
        }

        return $data;
    }

    /**
     * @param CategoryInterface $category
     *
     * @return array
     */
    protected function getNonVariantProductData(CategoryInterface $category): array
    {
        $products     = $this->productRepository->findNonVariantProductByCategory($category->getId());
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
     * @param CategoryInterface $category
     *
     * @return array
     */
    protected function getProductModelData(CategoryInterface $category): array
    {
        $productModels     = $this->productModelRepository->findByCategory($category->getId());
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
