<?php

namespace CL\Shipping\Test\Model;

use Harp\Harp\Config;
use CL\Shipping\ShippablePurchaseTrait;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class Purchase extends \CL\Purchases\Purchase
{
    use ShippablePurchaseTrait;

    public static function initialize(Config $config)
    {
        parent::initialize($config);

        ShippablePurchaseTrait::initialize($config);
    }
}
