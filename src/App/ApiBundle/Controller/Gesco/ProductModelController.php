<?php

namespace App\ApiBundle\Controller\Gesco;

use App\ApiBundle\Helper\LocaleHelper;
use App\ApiBundle\Normalizer\Gesco\ProductModelNormalizer;
use App\CacheBundle\Controller\AbstractController;
use Pim\Bundle\CatalogBundle\Doctrine\ORM\Repository\ProductModelRepository;
use Pim\Component\Catalog\Model\ProductModelInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ProductModelController.
 *
 * @package App\ApiBundle\Controller\Gesco
 * @author  Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class ProductModelController extends AbstractController
{
    /** @var ProductModelRepository */
    protected $productModelRepository;
    /** @var ProductModelNormalizer */
    protected $productModelNormalizer;

    /**
     * Get get product with family and attributes data.
     *
     * @param Request $request The request variable.
     * @param string  $code    The product code.
     *
     * @return JsonResponse
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getAction(Request $request, string $code): JsonResponse
    {
        $cacheItem = $this->getCachedItem($request);

        if (false === $cacheItem->isHit()) {
            /** @var ProductModelInterface $productModel */
            $productModel = $this->getProductModelRepository()->findOneByIdentifier($this->formatCode($code));

            if (null === $productModel) {
                throw new NotFoundHttpException(sprintf('No product model found for code "%s".', $code));
            }

            $this->getRedisManager()->setCacheData(
                $cacheItem,
                $this->getProductModelNormalizer()->normalize(
                    $productModel,
                    'gesco',
                    ['locale' => LocaleHelper::DEFAULT_LOCALE]
                )
            );
        }

        return new JsonResponse($this->getRedisManager()->getCacheData($cacheItem));
    }

    /**
     * @param string $code
     *
     * @return string
     */
    protected function formatCode(string $code): string
    {
        return \urldecode($code);
    }

    /**
     * Retrieve product model repository.
     *
     * @return ProductModelRepository
     */
    protected function getProductModelRepository(): ProductModelRepository
    {
        if (null === $this->productModelRepository) {
            $this->productModelRepository = $this->getService('pim_catalog.repository.product_model');
        }

        return $this->productModelRepository;
    }

    /**
     * Retrieve product model normalizer.
     *
     * @return ProductModelNormalizer
     */
    protected function getProductModelNormalizer(): ProductModelNormalizer
    {
        if (null === $this->productModelNormalizer) {
            $this->productModelNormalizer = $this->getService('app_api.normalizer.gesco.product_model');
        }

        return $this->productModelNormalizer;
    }
}
