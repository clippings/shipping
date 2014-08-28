<?php

namespace CL\Shipping;

use Harp\Harp\Config;
use Harp\Harp\Rel;

/**
 * This is an extension of CL\Purchases\ProductItem model
 *
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
trait ShippableProductItemTrait
{
    public static function initialize(Config $config)
    {
        $config
            ->addRels([
                new Rel\HasOne('shippingItem', $config, ShippingItem::getRepo(), [
                    'foreignKey' => 'parentId',
                    'inverseOf' => 'productItem',
                ]),
            ]);
    }

    /**
     * Get a shipping for the curreng shipping location of the purchase.
     *
     * @return Shipping
     */
    public function getDefaultShipping()
    {
        $location = $this->getPurchase()->getShippingLocation();

        return $this->getProduct()->getShippingProfile()->getDefaultShippingFor($location);
    }

    /**
     * @return ShippingItem
     */
    public function getShippingItem()
    {
        return $this->get('shippingItem');
    }

    /**
     * @param  ShippingItem $shippingItem
     * @return self
     */
    public function setShippingItem(ShippingItem $shippingItem)
    {
        $this->set('shippingItem', $shippingItem);

        return $this;
    }
}
