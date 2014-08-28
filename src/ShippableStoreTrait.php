<?php

namespace CL\Shipping;

use Harp\Harp\Config;
use Harp\Harp\Rel;

/**
 * This is an extension of CL\Purchases\Store model
 *
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
trait ShippableStoreTrait
{
    /**
     * @param  string $name
     * @return \Harp\Harp\Repo\LinkMany
     */
    abstract public function all($name);

    public static function initialize(Config $config)
    {
        $config
            ->addRels([
                new Rel\HasMany('shippingProfiles', $config, ShippingProfile::getRepo()),
                new Rel\HasMany('shippingMethods', $config, ShippingMethod::getRepo()),
            ]);
    }

    /**
     * @return \Harp\Harp\Repo\LinkMany
     */
    public function getShippingMethods()
    {
        return $this->all('shippingMethods');
    }

    /**
     * @return \Harp\Harp\Repo\LinkMany
     */
    public function getShippingProfiles()
    {
        return $this->all('shippingProfiles');
    }
}
