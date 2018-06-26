<?php

namespace App\ApiBundle\Normalizer\Gesco;

use Pim\Component\Catalog\Normalizer\Standard\DateTimeNormalizer as ParentNormalizer;

/**
 * Class DateTimeNormalizer.
 *
 * @package    App\ApiBundle\Normalizer\Gesco
 * @author     Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class DateTimeNormalizer extends ParentNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof \DateTime && 'gesco' === $format;
    }
}
