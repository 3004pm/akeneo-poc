<?php

namespace App\ApiBundle\Controller\Gesco;

use App\ApiBundle\Helper\LocaleHelper;
use App\ApiBundle\Normalizer\Gesco\ProductModelNormalizer;
use Pim\Bundle\CatalogBundle\Doctrine\ORM\Repository\ProductModelRepository;
use Pim\Component\Catalog\Model\ProductModelInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ProductModelController.
 *
 * @package App\ApiBundle\Controller\Gesco
 * @author  Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class ProductModelController
{
    /** @var ProductModelRepository */
    protected $productModelRepository;
    /** @var ProductModelNormalizer */
    protected $productModelNormalizer;

    /**
     * ProductModelController constructor.
     *
     * @param ProductModelRepository $productModelRepository
     * @param ProductModelNormalizer $productModelNormalizer
     */
    public function __construct(
        ProductModelRepository $productModelRepository,
        ProductModelNormalizer $productModelNormalizer
    ) {
        $this->productModelRepository = $productModelRepository;
        $this->productModelNormalizer = $productModelNormalizer;
    }

    /**
     * Get get product with family and attributes data.
     *
     * @param string $code The product code.
     *
     * @return JsonResponse
     */
    public function getAction(string $code): JsonResponse
    {
        /** @var ProductModelInterface $productModel */
        $productModel = $this->productModelRepository->findOneByIdentifier($this->formatCode($code));

        if (null === $productModel) {
            throw new NotFoundHttpException(sprintf('No product model found for code "%s".', $code));
        }

        return new JsonResponse(
            $this->productModelNormalizer->normalize(
                $productModel,
                'gesco',
                ['locale' => LocaleHelper::DEFAULT_LOCALE]
            )
        );
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
}
