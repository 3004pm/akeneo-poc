<?php

namespace App\ApiBundle\Controller\Gesco;

use App\ApiBundle\Helper\LocaleHelper;
use App\CacheBundle\Controller\AbstractController;
use Pim\Component\Catalog\Query\Filter\Operators;
use Pim\Component\Catalog\Query\ProductQueryBuilderFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ProductController.
 *
 * @package App\ApiBundle\Controller\Gesco
 * @author  Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class ProductController extends AbstractController
{
    /** @var ProductQueryBuilderFactoryInterface */
    protected $fromSizePqbFactory;

    /**
     * Get get product with family and attributes data.
     *
     * @param Request     $request The request variable.
     * @param string      $code    The product code.
     * @param string|null $firstAxeCode
     * @param string|null $firstAxeValue
     * @param string|null $sndAxeCode
     * @param string|null $sndAxeValue
     *
     * @return JsonResponse
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getAction(
        Request $request,
        string $code,
        string $firstAxeCode = null,
        string $firstAxeValue = null,
        string $sndAxeCode = null,
        string $sndAxeValue = null
    ): JsonResponse {
        $cacheItem = $this->getCachedItem($request);

        if (false === $cacheItem->isHit()) {
            $pqb = $this->getFromSizePqbFactory()->create(['limit' => 1, 'from' => 0]);
            $pqb->addFilter('identifier', Operators::EQUALS, $code);
            $products = $pqb->execute();

            if (0 === $products->count()) {
                throw new NotFoundHttpException(sprintf('Product "%s" does not exist.', $code));
            }

            $normalizedData = $this->getNormalizer()->normalize(
                $products->current(),
                'gesco',
                [
                    'locale'        => LocaleHelper::DEFAULT_LOCALE,
                    'firstAxeCode'  => $firstAxeCode,
                    'firstAxeValue' => $firstAxeValue,
                    'sndAxeCode'    => $sndAxeCode,
                    'sndAxeValue'   => $sndAxeValue,
                ]
            );

            $this->getRedisManager()->setCacheData($cacheItem, $normalizedData);
        }

        return new JsonResponse($this->getRedisManager()->getCacheData($cacheItem));
    }

    /**
     * Retrieve from size product query builder factory.
     *
     * @return ProductQueryBuilderFactoryInterface
     */
    protected function getFromSizePqbFactory(): ProductQueryBuilderFactoryInterface
    {
        if (null === $this->fromSizePqbFactory) {
            $this->fromSizePqbFactory = $this->getService('pim_catalog.query.product_query_builder_from_size_factory');
        }

        return $this->fromSizePqbFactory;
    }
}
