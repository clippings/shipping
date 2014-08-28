<?php

namespace CL\Shipping\Test;

use CL\Shipping\ShippingItem;
use CL\Shipping\ShippingManual;
use CL\Shipping\Shipping;
use CL\Purchases\Address;
use CL\Shipping\Test\Model\ProductItem;
use CL\Shipping\Test\Model\Purchase;
use Harp\Range\Range;
use Harp\Locations\Location;
use SebastianBergmann\Money\Currency;
use SebastianBergmann\Money\Money;

/**
 * @coversDefaultClass CL\Shipping\ShippingItem
 *
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class ShippingItemTest extends AbstractTestCase
{
    /**
     * @covers ::initialize
     * @covers ::getDescription
     */
    public function testInitialize()
    {
        $repo = ShippingItem::getRepo();

        $item = $repo->getRelOrError('productItem');
        $this->assertEquals('CL\Shipping\Test\Model\ProductItem', $item->getRepo()->getModelClass());

        $shipping = $repo->getRelOrError('shipping');
        $this->assertEquals('CL\Shipping\Shipping', $shipping->getRepo()->getModelClass());

        $item = new ShippingItem();
        $this->assertSame('Shipping', $item->getDescription());
    }

    /**
     * @covers ::getShipping
     * @covers ::setShipping
     */
    public function testShipping()
    {
        $item = new ShippingItem();

        $shipping = $item->getShipping();

        $this->assertInstanceOf('CL\Shipping\Shipping', $shipping);
        $this->assertTrue($shipping->isVoid());

        $shipping = new Shipping();

        $item->setShipping($shipping);

        $this->assertSame($shipping, $item->getShipping());
    }

    /**
     * @covers ::getProductItem
     * @covers ::setProductItem
     */
    public function testProductItem()
    {
        $item = new ShippingItem();

        $productItem = $item->getProductItem();

        $this->assertInstanceOf('CL\Shipping\Test\Model\ProductItem', $productItem);
        $this->assertTrue($productItem->isVoid());

        $productItem = new ProductItem();

        $item->setProductItem($productItem);

        $this->assertSame($productItem, $item->getProductItem());
    }

    /**
     * @covers ::getDeliveryDays
     * @covers ::getSourceDeliveryDays
     * @covers ::getSourceValue
     * @covers ::freezeDeliveryDays
     * @covers ::unfreezeDeliveryDays
     * @covers ::performFreeze
     * @covers ::performUnfreeze
     */
    public function testHeliveryDays()
    {
        $item = new ShippingItem();

        $shipping = $this->getMock('CL\Shipping\Shipping', ['getDeliveryDays', 'getValue']);
        $shipping
            ->expects($this->exactly(4))
            ->method('getDeliveryDays')
            ->will($this->returnValue(new Range(5, 10)));

        $shipping
            ->expects($this->exactly(4))
            ->method('getValue')
            ->will($this->returnValue(new Money(1200, new Currency('GBP'))));

        $item->setShipping($shipping);

        $this->assertEquals(new Range(5, 10), $item->getSourceDeliveryDays());
        $this->assertEquals(new Range(5, 10), $item->getDeliveryDays());
        $this->assertEquals(new Money(1200, new Currency('GBP')), $item->getSourceValue());
        $this->assertEquals(new Money(1200, new Currency('GBP')), $item->getValue());

        $item->freeze();

        $this->assertEquals(new Range(5, 10), $item->getDeliveryDays());
        $this->assertEquals(new Money(1200, new Currency('GBP')), $item->getValue());

        $item->setShipping(new Shipping());

        $this->assertEquals(new Range(5, 10), $item->getDeliveryDays());
        $this->assertEquals(new Money(1200, new Currency('GBP')), $item->getValue());

        $item->unfreeze();

        $this->assertEquals(new Range(0, 0), $item->getDeliveryDays());
        $this->assertEquals(new Money(0, new Currency('GBP')), $item->getValue());

        $item->setShipping($shipping);

        $this->assertEquals(new Range(5, 10), $item->getDeliveryDays());
        $this->assertEquals(new Money(1200, new Currency('GBP')), $item->getValue());
    }

    /**
     * @covers ::isStale
     */
    public function testIsStale()
    {
        $item = new ShippingItem();

        $this->assertTrue($item->isStale());

        $address = new Address();
        $address->setCity(Location::findByName('Sofia'));

        $purchase = new Purchase();
        $purchase->setShipping($address);
        $item->setPurchase($purchase);

        $shipping = new ShippingManual();
        $shipping->setLocation(Location::findByName('Bulgaria'));

        $item->setShipping($shipping);

        $this->assertFalse($item->isStale());
    }
}
