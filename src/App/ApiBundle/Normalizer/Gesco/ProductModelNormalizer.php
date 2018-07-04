<?php

namespace App\ApiBundle\Normalizer\Gesco;

use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Model\ProductModelInterface;
use Pim\Component\Catalog\Value\OptionValueInterface;

/**
 * Class ProductModelNormalizer.
 *
 * @package    App\ApiBundle\Normalizer\Gesco
 * @author     Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class ProductModelNormalizer
{
    /** @var FamilyNormalizer */
    protected $familyNormalizer;

    /**
     * ProductNormalizer constructor.
     *
     * @param FamilyNormalizer $familyNormalizer
     */
    public function __construct(FamilyNormalizer $familyNormalizer)
    {
        $this->familyNormalizer = $familyNormalizer;
    }

    /**
     * Normalize product model into format for gesco.
     *
     * @param ProductModelInterface $productModel The product model where to retrieve data.
     * @param string|null           $format       The normalizer format.
     * @param array                 $context      The context of normalization.
     *
     * @return array
     */
    public function normalize(ProductModelInterface $productModel, string $format = null, array $context = []): array
    {
        return [
            'identifier'  => $productModel->getLabel($context['locale'], null),
            'family'      => $this->normalizeFamily($productModel, $context['locale']),
            'variantData' => $this->getProductVariationAxeData($productModel),
            'productData' => $productModel->getRawValues(),
        ];
    }

    /**
     * Normalize family data.
     *
     * @param ProductModelInterface $productModel The product model where to retrieve family model.
     * @param string                $locale       The locale of normalize label.
     *
     * @return array
     */
    protected function normalizeFamily(ProductModelInterface $productModel, string $locale): array
    {
        if (null === $productModel->getFamilyVariant() || null === $productModel->getFamilyVariant()->getFamily()) {
            return [];
        }

        return $this->familyNormalizer->normalize(
            $productModel->getFamilyVariant()->getFamily(),
            null,
            ['locale' => $locale]
        );
    }

    /**
     * Retrieve product variation axe data.
     *
     * @param ProductModelInterface $productModel The product model of needed varation axe  data.
     *
     * @return array
     */
    protected function getProductVariationAxeData(ProductModelInterface $productModel): array
    {
        $axeCodes = $this->getAxeCodes($productModel);

        if (0 === \count($axeCodes)) {
            return [];
        }

        $variantData = [];

        /** @var ProductInterface $product */
        foreach ($productModel->getProducts() as $product) {
            $variantData[$product->getId()]['identifier'] = $product->getIdentifier();
            foreach ($axeCodes as $axeCode) {
                /** @var OptionValueInterface $optionValue */
                $optionValue                              = $product->getValue($axeCode);
                $variantData[$product->getId()][$axeCode] = null !== $optionValue->getData()
                    ? $optionValue->getData()->getCode()
                    : [];
            }
        }

        return $variantData;
    }

    /**
     * Retrieve axe attribute codes to normalize.
     *
     * @param ProductModelInterface $productModel The product model of attribute axe codes.
     *
     * @return array
     */
    protected function getAxeCodes(ProductModelInterface $productModel): array
    {
        if (null === $productModel->getFamilyVariant()) {
            return [];
        }

        $axeCodes = [];

        /** @var AttributeInterface $axe */
        foreach ($productModel->getFamilyVariant()->getAxes() as $axe) {
            $axeCodes[] = $axe->getCode();
        }

        return $axeCodes;
    }
}
