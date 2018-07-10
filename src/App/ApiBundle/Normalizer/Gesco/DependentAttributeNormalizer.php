<?php

namespace App\ApiBundle\Normalizer\Gesco;

use App\CustomEntityBundle\Entity\DependentAttribute;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class ProductNormalizer.
 *
 * @package    App\ApiBundle\Normalizer\Gesco
 * @author     Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class DependentAttributeNormalizer implements NormalizerInterface
{
    use NormalizerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function normalize($dependentAttribute, $format = null, array $context = [])
    {
        return $this->normalizer->normalize($dependentAttribute, 'standard', $context);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof DependentAttribute && 'gesco' === $format;
    }
}
