<?php

namespace App\ApiBundle\Normalizer\Gesco;

use Pim\Component\Catalog\Model\ProductInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class ProductNormalizer.
 *
 * @package    App\ApiBundle\Normalizer\Gesco
 * @author     Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class ProductNormalizer implements NormalizerInterface
{
    private const FIELD_ASSOCIATIONS = 'associations';

    /** @var NormalizerInterface */
    protected $propertiesNormalizer;

    /** @var NormalizerInterface */
    protected $associationsNormalizer;

    /**
     * ProductNormalizer constructor.
     *
     * @param NormalizerInterface $propertiesNormalizer
     * @param NormalizerInterface $associationsNormalizer
     */
    public function __construct(
        NormalizerInterface $propertiesNormalizer,
        NormalizerInterface $associationsNormalizer
    ) {
        $this->propertiesNormalizer = $propertiesNormalizer;
        $this->associationsNormalizer = $associationsNormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($product, $format = null, array $context = [])
    {
        $data = $this->propertiesNormalizer->normalize($product, $format, $context);

        if (!isset($context['isAssociation']) || false === $context['isAssociation']) {
            $context['isAssociation'] = true;
            $associatedData = $this->associationsNormalizer->normalize($product, $format, $context);
            $data[self::FIELD_ASSOCIATIONS] = $associatedData;
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ProductInterface && 'gesco' === $format;
    }
}
