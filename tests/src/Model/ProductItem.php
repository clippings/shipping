<?php

namespace CL\Shipping\Test\Model;

use Harp\Harp\Config;
use CL\Shipping\ShippableProductItemTrait;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class ProductItem extends \CL\Purchases\ProductItem
{
    use ShippableProductItemTrait;

    public static function initialize(Config $config)
    {
        parent::initialize($config);

        ShippableProductItemTrait::initialize($config);
    }
}
