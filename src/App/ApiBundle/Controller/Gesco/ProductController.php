<?php

namespace App\ApiBundle\Controller\Gesco;

use App\ApiBundle\Helper\LocaleHelper;
use Pim\Component\Catalog\Query\Filter\Operators;
use Pim\Component\Catalog\Query\ProductQueryBuilderFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

/**
 * Class ProductController.
 *
 * @package    App\ApiBundle\Controller\Gesco
 * @author     Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 * @version    1.0
 */
class ProductController
{
    use NormalizerAwareTrait;

    /** @var ProductQueryBuilderFactoryInterface */
    protected $fromSizePqbFactory;

    /**
     * ProductController constructor.
     *
     * @param ProductQueryBuilderFactoryInterface $fromSizePqbFactory
     */
    public function __construct(ProductQueryBuilderFactoryInterface $fromSizePqbFactory)
    {
        $this->fromSizePqbFactory = $fromSizePqbFactory;
    }

    /**
     * Get get product with family and attributes data.
     *
     * @param string      $code   The product code.
     * @param string|null $firstAxeCode
     * @param string|null $firstAxeValue
     * @param string|null $sndAxeCode
     * @param string|null $sndAxeValue
     *
     * @return JsonResponse
     */
    public function getAction(
        string $code,
        string $firstAxeCode = null,
        string $firstAxeValue = null,
        string $sndAxeCode = null,
        string $sndAxeValue = null
    ): JsonResponse {
        $pqb = $this->fromSizePqbFactory->create(['limit' => 1, 'from' => 0]);
        $pqb->addFilter('identifier', Operators::EQUALS, $code);
        $products = $pqb->execute();

        if (0 === $products->count()) {
            throw new NotFoundHttpException(sprintf('Product "%s" does not exist.', $code));
        }

        return new JsonResponse(
            $this->normalizer->normalize(
                $products->current(),
                'gesco',
                [
                    'locale'        => LocaleHelper::DEFAULT_LOCALE,
                    'firstAxeCode'  => $firstAxeCode,
                    'firstAxeValue' => $firstAxeValue,
                    'sndAxeCode'    => $sndAxeCode,
                    'sndAxeValue'   => $sndAxeValue,
                ]
            )
        );
    }
}