<?php

namespace App\ApiBundle\Controller;

use \Pim\Bundle\ApiBundle\Controller\ProductController as ParentController;
use Pim\Component\Catalog\Query\Filter\Operators;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ProductController.
 *
 * @package    App\ApiBundle\Controller
 * @author     Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 * @version    1.0
 */
class ProductController extends ParentController
{
    /**
     * Get get product with family and attributes data.
     *
     * @param string $code
     *
     * @return JsonResponse
     */
    public function getGescoAction(string $code, string $locale): JsonResponse
    {
        $pqb = $this->fromSizePqbFactory->create(['limit' => 1, 'from' => 0]);
        $pqb->addFilter('identifier', Operators::EQUALS, $code);
        $products = $pqb->execute();

        if (0 === $products->count()) {
            throw new NotFoundHttpException(sprintf('Product "%s" does not exist.', $code));
        }

        return new JsonResponse(
            $this->normalizer->normalize($products->current(), 'gesco', ['locale' => $locale])
        );
    }
}
