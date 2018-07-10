<?php

namespace App\CustomEntityBundle\Normalizer\Standard;

use App\CustomEntityBundle\Entity\DependentAttribute;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class DependentAttributeNormalizer.
 *
 * @package App\CustomEntityBUndle\Normalizer\Standard
 * @author  Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class DependentAttributeNormalizer implements NormalizerInterface
{
    /** @var string[] */
    protected $supportedFormats = ['standard', 'flat'];

    /**
     * @param DependentAttribute $entity
     * @param null               $format
     * @param array              $context
     *
     * @return array
     */
    public function normalize($entity, $format = null, array $context = [])
    {
        return [
            'id'                     => $entity->getId(),
            'code'                   => $entity->getCode(),
            'attributeCode'          => $entity->getAttributeCode(),
            'optionCode'             => $entity->getOptionCode(),
            'dependentAttributeCode' => $entity->getDependentAttributeCode(),
            'dependentOptionCode'    => $entity->getDependentOptionCode(),
        ];
    }

    /**
     * @param mixed $data
     * @param null  $format
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof DependentAttribute && \in_array($format, $this->supportedFormats, false);
    }
}
