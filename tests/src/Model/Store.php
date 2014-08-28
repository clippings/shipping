<?php

namespace CL\Shipping\Test\Model;

use Harp\Harp\Config;
use CL\Shipping\ShippableStoreTrait;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class Store extends \CL\Purchases\Store
{
    use ShippableStoreTrait;

    public static function initialize(Config $config)
    {
        parent::initialize($config);

        ShippableStoreTrait::initialize($config);
    }
}
