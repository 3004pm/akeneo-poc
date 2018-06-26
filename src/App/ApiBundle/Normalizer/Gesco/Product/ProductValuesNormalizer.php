<?php

namespace App\ApiBundle\Normalizer\Gesco\Product;

use Pim\Component\Catalog\Model\ValueCollectionInterface;
use Pim\Component\Catalog\Normalizer\Standard\Product\ProductValuesNormalizer as ParentNormalizer;

/**
 * Class ProductValuesNormalizer.
 *
 * @package    App\ApiBundle\Normalizer\Gesco\Product
 * @author     Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class ProductValuesNormalizer extends ParentNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ValueCollectionInterface && 'gesco' === $format;
    }
}
