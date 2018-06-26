<?php

namespace App\ApiBundle\Normalizer\Gesco\Product;

use ArrayAccess;
use Pim\Component\Catalog\AttributeTypes;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\AttributeOptionInterface;
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

    /**
     * {@inheritdoc}
     */
    public function normalize($product, $format = null, array $context = [])
    {
        $data = parent::normalize($product, $format, $context);

        $this->locale = $context['locale'];

        $family = $product->getFamily();

        $data[self::FIELD_FAMILY] = [
            'code'       => $family ? $family->getCode() : null,
            'attributes' => $family ? $this->getNormalizedGroupedAttributes($family) : [],
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

    /**
     * @param FamilyInterface $family
     *
     * @return array
     */
    protected function getNormalizedGroupedAttributes(FamilyInterface $family): array
    {
        $groupedAttributesData = [];

        /** @var AttributeInterface[] $attributes */
        foreach ($family->getGroupedAttributes() as $group => $attributes) {
            $groupedAttributesData[$group] = $this->getNormalizedAttributes($attributes);
        }

        return $groupedAttributesData;
    }

    /**
     * @param AttributeInterface[] $attributes
     *
     * @return array
     */
    protected function getNormalizedAttributes(array $attributes): array
    {
        $attributesData = [];

        /** @var AttributeInterface $attribute */
        foreach ($attributes as $attribute) {
            $attributeCode = $attribute->getCode();

            $attributesData[$attributeCode] = [
                'type'  => $attribute->getType(),
                'label' => (string)$attribute->setLocale($this->locale),
            ];

            if (in_array($attribute->getType(), $this->getAttributeTypesWithOptions())) {
                $attributesData[$attributeCode]['options'] = $this->getNormalizedAttributeOptions(
                    $attribute->getOptions()
                );
            }
        }

        return $attributesData;
    }

    /**
     * getNormalizedAttributeOptions
     *
     * @param ArrayAccess $attributeOptions
     *
     * @return array
     */
    protected function getNormalizedAttributeOptions(ArrayAccess $attributeOptions): array
    {
        $attributeOptionsData = [];

        /** @var AttributeOptionInterface $attributeOption */
        foreach ($attributeOptions as $attributeOption) {
            $attributeOptionsData[$attributeOption->getCode()]['label'] = (string)$attributeOption->setLocale(
                $this->locale
            );
        }

        return $attributeOptionsData;
    }

    /**
     * Retrieve attribute types where attributes deal with options.
     *
     * @return array
     */
    protected function getAttributeTypesWithOptions(): array
    {
        return [
            AttributeTypes::OPTION_SIMPLE_SELECT,
            AttributeTypes::OPTION_MULTI_SELECT,
            AttributeTypes::REFERENCE_DATA_SIMPLE_SELECT,
            AttributeTypes::REFERENCE_DATA_MULTI_SELECT
        ];
    }
}
