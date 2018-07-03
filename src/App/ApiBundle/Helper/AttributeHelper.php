<?php

namespace App\ApiBundle\Helper;

use Pim\Component\Catalog\AttributeTypes;

/**
 * Class AttributeHelper.
 *
 * @package    App\ApiBundle\Helper
 * @copyright  Copyright (c) 2017 CGI (http://www.cgi.com)
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author     Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 * @version    1.0
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