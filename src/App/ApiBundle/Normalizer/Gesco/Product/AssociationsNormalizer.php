<?php

namespace App\ApiBundle\Normalizer\Gesco\Product;

use Doctrine\Common\Collections\Collection;
use Pim\Component\Catalog\Model\AssociationInterface;
use Pim\Component\Catalog\Model\AttributeOptionInterface;
use Pim\Component\Catalog\Model\EntityWithAssociationsInterface;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Model\ProductModelInterface;
use Pim\Component\Catalog\Model\ValueInterface;
use Pim\Component\Catalog\Normalizer\Standard\Product\AssociationsNormalizer as ParentNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

/**
 * Class AssociationsNormalizer.
 *
 * @package    App\ApiBundle\Normalizer\Gesco\Product
 * @author     Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class AssociationsNormalizer extends ParentNormalizer implements NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    /**
     * {@inheritdoc}
     *
     * @param EntityWithAssociationsInterface $associationAwareEntity
     */
    public function normalize($associationAwareEntity, $format = null, array $context = [])
    {
        $data = [];

        /** @var AssociationInterface $productAssociation */
        foreach ($associationAwareEntity->getAllAssociations() as $productAssociation) {
            $this->normalizeAssociatedProducts($productAssociation->getProducts(), $format, $context, $data);
            $this->normalizeAssociatedProductModels($productAssociation->getProductModels(), $format, $context, $data);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof EntityWithAssociationsInterface && 'gesco' === $format;
    }

    /**
     * Normalize the given product.
     *
     * @param ProductInterface $product The given product to normalize.
     * @param string|null      $format  The normalizer format.
     * @param array            $context The context of normalization.
     * @param array            $data    The futur exported data.
     */
    protected function normalizeAssociatedProduct(
        ProductInterface $product,
        string $format = null,
        array $context = [],
        array &$data
    ): void {
        if (
            true === $this->isEligible($product, $context)
            && !\array_key_exists($product->getIdentifier(), $data)
        ) {
            $data[$product->getIdentifier()] = $this->normalizer->normalize(
                $product,
                $format,
                $context
            );
        }
    }

    /**
     * Normalize products associated to our product.
     *
     * @param ProductInterface[]|Collection $associatedProducts The collection of associated products.
     * @param string|null                   $format             The normalizer format.
     * @param array                         $context            The context of normalization.
     * @param array                         $data               The futur exported data.
     */
    protected function normalizeAssociatedProducts(
        $associatedProducts,
        string $format = null,
        array $context = [],
        array &$data
    ): void {
        /** @var ProductInterface $associatedProduct */
        foreach ($associatedProducts as $associatedProduct) {
            $this->normalizeAssociatedProduct($associatedProduct, $format, $context, $data);
        }
    }

    /**
     * Normalize products from product model associated to our product.
     *
     * @param ProductModelInterface[]|Collection $productModels The collection of associated product models
     * @param string|null                        $format        The normalizer format.
     * @param array                              $context       The context of normalization.
     * @param array                              $data          The futur exported data.
     */
    protected function normalizeAssociatedProductModels(
        $productModels,
        string $format = null,
        array $context = [],
        array &$data
    ): void {
        /** @var ProductModelInterface $productModel */
        foreach ($productModels as $productModel) {
            $this->normalizeAssociatedProducts($productModel->getProducts(), $format, $context, $data);
        }
    }

    /**
     * Check if associated product is eligible to be export by API.
     *
     * @param ProductInterface $product The associated product to check.
     * @param array            $context The context of normalizatoin to retrieve attribute code axe.
     *
     * @return bool
     */
    protected function isEligible(ProductInterface $product, array $context): bool
    {
        if (false === $product->isEnabled()) {
            return false;
        }

        if (false === $product->isVariant()) {
            return true;
        }

        // If no axe is defined in api call.
        if (null === $context['firstAxeCode'] && null === $context['sndAxeCode']) {
            return true;
        }

        if (
            null !== $context['firstAxeCode']
            && false === $this->checkAxeValue($product, $context['firstAxeCode'], $context['firstAxeValue'])
        ) {
            return false;
        }

        if (
            null !== $context['sndAxeCode']
            && false === $this->checkAxeValue($product, $context['sndAxeCode'], $context['sndAxeValue'])
        ) {
            return false;
        }

        return true;
    }

    /**
     * Check product axe.
     *
     * @param ProductInterface $product  The product where to check axe.
     * @param string           $axeCode  The attribute code to check as axe.
     * @param string           $axeValue The value's axe needed.
     *
     * @return bool
     */
    protected function checkAxeValue(ProductInterface $product, string $axeCode, string $axeValue): bool
    {
        /** @var ValueInterface $value */
        $value = $product->getValue($axeCode);

        // If product does not have attribute as axe
        if (!$value instanceof ValueInterface) {
            return true;
        }

        /** @var AttributeOptionInterface $valueData */
        $valueData = $value->getData();

        // If product axe is not fill
        if (!$valueData instanceof AttributeOptionInterface) {
            return false;
        }

        return $axeValue === $valueData->getCode();
    }
}
