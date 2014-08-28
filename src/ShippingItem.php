<?php

namespace CL\Shipping;

use Harp\Harp\Config;
use Harp\Harp\Rel;
use Harp\Range\Range;
use CL\Purchases\PurchaseItem;
use CL\Purchases\ProductItem;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class ShippingItem extends PurchaseItem
{
    public static function initialize(Config $config)
    {
        parent::initialize($config);

        $config
            ->addRels([
                new Rel\BelongsTo('productItem', $config, ProductItem::getRepo(), [
                    'key' => 'parentId',
                    'inverseOf' => 'shippingItem',
                ]),
                new Rel\BelongsTo('shipping', $config, Shipping::getRepo(), [
                    'key' => 'sourceId',
                ]),
            ]);
    }

    /**
     * @var integer
     */
    public $parentId;

    /**
     * @var integer
     */
    public $sourceId;

    /**
     * @var string
     */
    public $parameter;

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Shipping';
    }

    /**
     * Check if this shipping is valid for the purchase's shipping location
     *
     * @return boolean
     */
    public function isStale()
    {
        $location = $this->getPurchase()->getShippingLocation();

        return ! $this->getShipping()->hasLocation($location);
    }

    /**
     * @return \SebastianBergmann\Money\Money
     */
    public function getSourceValue()
    {
        return $this->getShipping()->getValue();
    }

    /**
     * This is a freezable value
     *
     * @return Range
     */
    public function getDeliveryDays()
    {
        return $this->isFrozen
            ? (new Range())->unserialize($this->parameter)
            : $this->getSourceDeliveryDays();
    }

    /**
     * Get delivery days Range object form the shipping
     *
     * @return Range
     */
    public function getSourceDeliveryDays()
    {
        return $this->getShipping()->getDeliveryDays();
    }

    /**
     * @return self
     */
    public function freezeDeliveryDays()
    {
        $this->parameter = $this->getSourceDeliveryDays()->serialize();

        return $this;
    }

    /**
     * @return self
     */
    public function unfreezeDeliveryDays()
    {
        $this->parameter = null;

        return $this;
    }

    /**
     * Implement FreezableTrait
     */
    public function performFreeze()
    {
        $this
            ->freezeValue()
            ->freezeDeliveryDays();
    }

    /**
     * Implement FreezableTrait
     */
    public function performUnfreeze()
    {
        $this
            ->unfreezeValue()
            ->unfreezeDeliveryDays();
    }

    /**
     * @return Shipping
     */
    public function getShipping()
    {
        return $this->get('shipping');
    }

    /**
     * @param  Shipping $shipping
     * @return self
     */
    public function setShipping(Shipping $shipping)
    {
        $this->set('shipping', $shipping);

        return $this;
    }

    /**
     * @return ProductItem
     */
    public function getProductItem()
    {
        return $this->get('productItem');
    }

    /**
     * @param  ProductItem $productItem
     * @return self
     */
    public function setProductItem(ProductItem $productItem)
    {
        $this->set('productItem', $productItem);

        return $this;
    }
}
