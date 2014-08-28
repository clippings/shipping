<?php

namespace CL\Shipping;

use Harp\Harp\Config;
use Harp\Harp\Rel;
use Harp\Harp\AbstractModel;

/**
 * This is an extension of CL\Purchases\ProductItem model
 *
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
trait ShippableProductItemTrait
{
    /**
     * @return \CL\Purchases\Purchase
     */
    abstract public function getPurchase();

    /**
     * @return \CL\Purchases\Product
     */
    abstract public function getProduct();

    /**
     * @param  $name         string
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
     */
    public function setShippingItem(ShippingItem $shippingItem)
    {
        $this->set('shippingItem', $shippingItem);

        return $this;
    }
}
