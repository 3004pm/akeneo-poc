<?php

namespace App\ApiBundle\Helper;

use Pim\Component\Catalog\AttributeTypes;

/**
 * Class AttributeHelper.
 *
 * @package    App\ApiBundle\Helper
 * @author     Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class AttributeHelper
{
    /**
     * Retrieve attribute types where attributes deal with options.
     *
     * @return array
     */
    public static function getAttributeTypesWithOptions(): array
    {
        return [
            AttributeTypes::OPTION_SIMPLE_SELECT,
            AttributeTypes::OPTION_MULTI_SELECT,
            AttributeTypes::REFERENCE_DATA_SIMPLE_SELECT,
            AttributeTypes::REFERENCE_DATA_MULTI_SELECT
        ];
    }
}