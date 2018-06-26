<?php

namespace App\ApiBundle\Normalizer\Gesco\Product;

use Pim\Component\Catalog\Model\ValueInterface;
use Pim\Component\Catalog\Normalizer\Standard\Product\ProductValueNormalizer as ParentNormalizer;

/**
 * Class ProductValueNormalizer.
 *
 * @package    App\ApiBundle\Normalizer\Gesco\Product
 * @author     Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class ProductValueNormalizer extends ParentNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof ValueInterface && 'gesco' === $format;
    }
}
