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
     * @param FamilyInterface $family
     * @param null            $format
     * @param array           $context
     *
     * @return array
     */
    public function normalize($family, $format = null, array $context = []): array
    {
        return [
            'code'  => $family->getCode(),
            'model' => $this->getNormalizedGroupedAttributes($family, $context),
        ];
    }

    /**
     * @param FamilyInterface $family
     * @param array           $context
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
     * @param AttributeInterface[] $attributes
     * @param array                $context
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
     * @param ArrayAccess $attributeOptions
     * @param array       $context
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
