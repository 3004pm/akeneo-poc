<?php

namespace App\ApiBundle\Normalizer\Gesco\Product;

use Pim\Component\Catalog\Model\MetricInterface;
use Pim\Component\Catalog\Normalizer\Standard\Product\MetricNormalizer as ParentNormalizer;

/**
 * Class MetricNormalizer.
 *
 * @package    App\ApiBundle\Normalizer\Gesco\Product
 * @author     Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class MetricNormalizer extends ParentNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof MetricInterface && 'gesco' === $format;
    }
}
