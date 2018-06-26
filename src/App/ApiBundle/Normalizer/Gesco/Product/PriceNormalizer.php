<?php

namespace App\ApiBundle\Normalizer\Gesco\Product;

use Pim\Component\Catalog\Model\ProductPriceInterface;
use Pim\Component\Catalog\Normalizer\Standard\Product\PriceNormalizer as ParentNormalizer;

/**
 * Class PriceNormalizer.
 *
 * @package    App\ApiBundle\Normalizer\Standard\Product
 * @author     Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class PriceNormalizer extends ParentNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ProductPriceInterface && 'gesco' === $format;
    }
}
