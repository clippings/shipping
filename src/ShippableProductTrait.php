<?php

namespace CL\Shipping;

use Harp\Harp\Config;
use Harp\Harp\Rel;
use Harp\Harp\AbstractModel;

/**
 * This is an extension of CL\Purchases\Product model
 *
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
trait ShippableProductTrait
{
    /**
     * @param  string $name
     * @return AbstractModel
     */
    abstract public function get($name);

    /**
     * @param string        $name
     * @param AbstractModel $model
     */
    abstract public function set($name, AbstractModel $model);

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
     * @param ShippingProfile $shippingProfile
     */
    public function setShippingProfile(ShippingProfile $shippingProfile)
    {
        $this->set('shippingProfile', $shippingProfile);

        return $this;
    }
}
