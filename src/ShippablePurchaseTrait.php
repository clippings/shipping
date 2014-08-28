<?php

namespace CL\Shipping;

use Harp\Harp\Config;
use Harp\Harp\Rel;
use CL\Purchases\PurchaseItem;
use CL\Purchases\Address;
use Harp\Locations\Location;
use Harp\Money\MoneyObjects;
use InvalidArgumentException;

/**
 * This is an extension of CL\Purchases\Purchase model
 *
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
trait ShippablePurchaseTrait
{
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

    /**
     * @return \Harp\Harp\Repo\LinkMany
     */
    abstract public function getItems();

    /**
     * @return \Harp\Harp\Repo\LinkMany
     */
    abstract public function getProductItems();

    public static function initialize(Config $config)
    {
        $config
            ->addRels([
                new Rel\BelongsTo('shipping', $config, Address::getRepo()),
            ]);
    }

    /**
     * Return either the City of the shipping, or if its void, the country
     *
     * @return Location
     */
    public function getShippingLocation()
    {
        $address = $this->getShipping();

        return $address->getCity()->isVoid()
            ? $address->getCountry()
            : $address->getCity();
    }

    /**
     * Set this as the shipping location
     *
     * @param  Location $location
     */
    public function setShippingLocation(Location $location)
    {
        if ($location->isCity()) {
            $this->getShipping()->setCity($location);
        } elseif ($location->isCountry()) {
            $this->getShipping()->setCountry($location);
        } else {
            throw new InvalidArgumentException('Must be Harp\Locations\City or Harp\Locations\Country');
        }

        return $this;
    }

    /**
     * Iterate through product items and if their shipping items are void or stale, update them with the default shipping
     */
    public function updateShippingItems()
    {
        foreach ($this->getProductItems() as $item) {
            $shippingItem = $item->getShippingItem();

            if ($shippingItem->isVoid()) {
                $shippingItem = new ShippingItem();
                $shippingItem->setShipping($item->getDefaultShipping());

                $item->getStorePurchase()->addPurchaseItem($shippingItem);
                $item->setShippingItem($shippingItem);
            } elseif ($shippingItem->isStale()) {
                $shippingItem->setShipping($item->getDefaultShipping());
            }

            $shippingItem->quantity = $item->quantity;
        }

        return $this;
    }

    /**
     * @return \Harp\Harp\Model\Models
     */
    public function getShippingItems()
    {
        return $this->getItems()->filter(function (PurchaseItem $item) {
            return $item instanceof ShippingItem;
        });
    }

    /**
     * @return \SebastianBergmann\Money\Money
     */
    public function getShippingItemsValue()
    {
        return MoneyObjects::sum($this->getShippingItems()->invoke('getValue'));
    }

    /**
     * @return Address
     */
    public function getShipping()
    {
        return $this->get('shipping');
    }

    /**
     * @param  Address $shipping
     * @return self
     */
    public function setShipping(Address $shipping)
    {
        $this->set('shipping', $shipping);

        return $this;
    }
}
