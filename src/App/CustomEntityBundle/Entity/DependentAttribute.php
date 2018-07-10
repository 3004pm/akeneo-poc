<?php

namespace App\CustomEntityBundle\Entity;

use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;

/**
 * Class DependentAttribute.
 *
 * @package App\CustomEntityBundle\Entity
 * @author  Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class DependentAttribute extends AbstractCustomEntity
{
    /** @var string */
    protected $attributeCode;
    /** @var string */
    protected $optionCode;
    /** @var string */
    protected $dependentAttributeCode;
    /** @var string */
    protected $dependentOptionCode;

    /**
     * @return string
     */
    public function getAttributeCode(): string
    {
        return $this->attributeCode;
    }

    /**
     * @param string $attributeCode
     *
     * @return DependentAttribute
     */
    public function setAttributeCode(string $attributeCode): DependentAttribute
    {
        $this->attributeCode = $attributeCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getOptionCode(): string
    {
        return $this->optionCode;
    }

    /**
     * @param string $optionCode
     *
     * @return DependentAttribute
     */
    public function setOptionCode(string $optionCode): DependentAttribute
    {
        $this->optionCode = $optionCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getDependentAttributeCode(): string
    {
        return $this->dependentAttributeCode;
    }

    /**
     * @param string $dependentAttributeCode
     *
     * @return DependentAttribute
     */
    public function setDependentAttributeCode(string $dependentAttributeCode): DependentAttribute
    {
        $this->dependentAttributeCode = $dependentAttributeCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getDependentOptionCode(): string
    {
        return $this->dependentOptionCode;
    }

    /**
     * @param string $dependentOptionCode
     *
     * @return DependentAttribute
     */
    public function setDependentOptionCode(string $dependentOptionCode): DependentAttribute
    {
        $this->dependentOptionCode = $dependentOptionCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomEntityName(): string
    {
        return 'dependentattribute';
    }
}