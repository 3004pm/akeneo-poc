<?php

namespace App\ApiBundle\Normalizer\Gesco;

use App\ApiBundle\Helper\AttributeHelper;
use ArrayAccess;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\AttributeOptionInterface;
use Pim\Component\Catalog\Model\FamilyInterface;

/**
 * Class FamilyNormalizer.
 *
 * @package    App\ApiBundle\Normalizer\Gesco
 * @author     Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class FamilyNormalizer
{
    /**
     * {@inheritdoc}
     *
     * @param FamilyInterface $family  The family to normalize.
     * @param string|null     $format  The normalizer format.
     * @param array           $context The context of normalization.
     *
     * @return array
     */
    public function normalize($family, string $format = null, array $context = []): array
    {
        return [
            'code'  => $family->getCode(),
            'model' => $this->getNormalizedGroupedAttributes($family, $context),
        ];
    }

    /**
     * Retrieve normalized grouped attributes.
     *
     * @param FamilyInterface $family  The family where to retrieve grouped attributes.
     * @param array           $context The context of normalization.
     *
     * @return array
     */
    protected function getNormalizedGroupedAttributes(FamilyInterface $family, array $context): array
    {
        $groupedAttributesData = [];

        /** @var AttributeInterface[] $attributes */
        foreach ($family->getGroupedAttributes() as $group => $attributes) {
            $groupedAttributesData[$group] = $this->getNormalizedAttributes($attributes, $context);
        }

        return $groupedAttributesData;
    }

    /**
     * Retrieve normalized attributes
     *
     * @param AttributeInterface[] $attributes The attributes to normalize.
     * @param array                $context    The context of normalization.
     *
     * @return array
     */
    protected function getNormalizedAttributes(array $attributes, array $context): array
    {
        $attributesData = [];

        /** @var AttributeInterface $attribute */
        foreach ($attributes as $attribute) {
            $attributeCode = $attribute->getCode();

            $attributesData[$attributeCode] = [
                'type'  => $attribute->getType(),
                'label' => (string)$attribute->setLocale($context['locale']),
            ];

            if (\in_array($attribute->getType(), AttributeHelper::getAttributeTypesWithOptions(), false)) {
                $attributesData[$attributeCode]['options'] = $this->getNormalizedAttributeOptions(
                    $attribute->getOptions(),
                    $context
                );
            }
        }

        return $attributesData;
    }

    /**
     * Retrieve normalized attribute options.
     *
     * @param ArrayAccess $attributeOptions The attribute options to normalize.
     * @param array       $context          The context of normalization.
     *
     * @return array
     */
    protected function getNormalizedAttributeOptions(ArrayAccess $attributeOptions, array $context): array
    {
        $attributeOptionsData = [];

        /** @var AttributeOptionInterface $attributeOption */
        foreach ($attributeOptions as $attributeOption) {
            $attributeOptionsData[$attributeOption->getCode()]['label'] = (string)$attributeOption->setLocale(
                $context['locale']
            );
        }

        return $attributeOptionsData;
    }
}
