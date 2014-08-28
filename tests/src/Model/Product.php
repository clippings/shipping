<?php

namespace CL\Shipping\Test\Model;

use Harp\Harp\Config;
use CL\Shipping\ShippableProductTrait;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class Product extends \CL\Purchases\Product
{
    use ShippableProductTrait;

    public static function initialize(Config $config)
    {
        parent::initialize($config);

        ShippableProductTrait::initialize($config);
    }
}
