<?php

namespace CL\Shipping;

use Harp\Harp\Config;
use Harp\Harp\Rel;

/**
 * This is an extension of CL\Purchases\Product model
 *
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
trait ShippableProductTrait
{
    public static function initialize(Config $config)
    {
        $config
            ->addRels([
                new Rel\BelongsTo('shippingProfile', $config, ShippingProfile::getRepo()),
            ]);
    }

    /**
     * @return ShippingProfile
     */
    public function getShippingProfile()
    {
        return $this->get('shippingProfile');
    }

    /**
     * @param  ShippingProfile $shippingProfile
     * @return self
     */
    public function setShippingProfile(ShippingProfile $shippingProfile)
    {
        $this->set('shippingProfile', $shippingProfile);

        return $this;
    }
}
