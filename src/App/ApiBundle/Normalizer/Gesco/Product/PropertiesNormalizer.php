<?php

namespace App\ApiBundle\Normalizer\Gesco\Product;

use App\ApiBundle\Normalizer\Gesco\FamilyNormalizer;
use Pim\Bundle\CatalogBundle\Filter\CollectionFilterInterface;
use Pim\Component\Catalog\Model\FamilyInterface;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Normalizer\Standard\Product\PropertiesNormalizer as ParentNormalizer;

/**
 * Class PropertiesNormalizer.
 *
 * @package    App\ApiBundle\Normalizer\Gesco\Product
 * @author     Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class PropertiesNormalizer extends ParentNormalizer
{
    /** @var string */
    protected $locale;

    /** @var FamilyNormalizer */
    protected $familyNormalizer;

    /**
     * PropertiesNormalizer constructor.
     *
     * @param CollectionFilterInterface $filter
     * @param FamilyNormalizer          $familyNormalizer
     */
    public function __construct(
        CollectionFilterInterface $filter,
        FamilyNormalizer $familyNormalizer
    ) {
        parent::__construct($filter);

        $this->familyNormalizer = $familyNormalizer;
    }

    /**
     * @param ProductInterface $product
     * @param null             $format
     * @param array            $context
     *
     * @return array|\Symfony\Component\Serializer\Normalizer\scalar
     */
    public function normalize($product, $format = null, array $context = [])
    {
        $data = parent::normalize($product, $format, $context);

        $this->locale = $context['locale'];

        /** @var FamilyInterface $family */
        $family = $product->getFamily();

        $data[self::FIELD_FAMILY] = [
            'code'       => $family ? $family->getCode() : null,
            'attributes' => $family ? $this->familyNormalizer->normalize($family, $format, $context) : [],
        ];

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
