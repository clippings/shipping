<?php

namespace CL\Shipping\Test;

use CL\Shipping\Test\Model\Purchase;
use CL\Shipping\Test\Model\Store;
use CL\Shipping\Test\Model\Product;
use CL\Shipping\Test\Model\ProductItem;
use CL\Shipping\Shipping;
use CL\Purchases\Address;
use Harp\Locations\Country;
use Harp\Locations\City;
use Harp\Locations\Region;
use SebastianBergmann\Money\Money;

/**
 * @coversDefaultClass CL\Shipping\ShippablePurchaseTrait
 *
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class ShippablePurchaseTraitTest extends AbstractTestCase
{
    /**
     * @covers ::initialize
     */
    public function testInitialize()
    {
        $repo = Purchase::getRepo();

        $shipping = $repo->getRelOrError('shipping');
        $this->assertEquals('CL\Purchases\Address', $shipping->getRepo()->getModelClass());
    }

    /**
     * @covers ::getShippingLocation
     */
    public function testGetShippingLocation()
    {
        $purchase = new Purchase();

        $location = $purchase->getShippingLocation();

        $this->assertInstanceOf('Harp\Locations\Country', $location);
        $this->assertTrue($location->isVoid());

        $country = new Country();
        $shipping = new Address();
        $shipping->setCountry($country);

        $purchase->setShipping($shipping);

        $this->assertSame($country, $purchase->getShippingLocation());

        $city = new City();
        $shipping->setCity($city);

        $this->assertSame($city, $purchase->getShippingLocation());
    }

    /**
     * @covers ::getShippingItems
     */
    public function testGetShippingItems()
    {
        $purchase = Purchase::find(1);

        $items = $purchase->getShippingItems();

        $this->assertContainsOnlyInstancesOf('CL\Shipping\ShippingItem', $items);
        $this->assertEquals(array(5, 6, 7, 8), $items->getIds());
    }

    /**
     * @covers ::getShippingItemsValue
     */
    public function testGetShippingItemsValue()
    {
        $purchase = Purchase::find(1);

        $value = $purchase->getShippingItemsValue();

        $this->assertEquals(new Money(8200, $purchase->getCurrency()), $value);
    }

    /**
     * @covers ::setShippingLocation
     */
    public function testSetShippingLocation()
    {
        $purchase = new Purchase();
        $shipping = new Address();
        $country = new Country();
        $city = new City();
        $region = new Region();

        $purchase->setShipping($shipping);

        $purchase->setShippingLocation($country);

        $this->assertSame($country, $shipping->getCountry());

        $purchase->setShippingLocation($country);

        $this->assertSame($country, $shipping->getCountry());

        $purchase->setShippingLocation($city);

        $this->assertSame($city, $shipping->getCity());

        $this->setExpectedException('InvalidArgumentException', 'Must be Harp\Locations\City or Harp\Locations\Country');

        $purchase->setShippingLocation($region);
    }

    /**
     * @covers ::updateShippingItems
     */
    public function testUpdateShippingItemsVoid()
    {
        $purchase = new Purchase();
        $shipping = new Shipping();
        $store = new Store();
        $product = new Product();

        $product->setStore($store);

        $item = $this->getMock('CL\Shipping\Test\Model\ProductItem', ['getDefaultShipping'], [['quantity' => 10]]);
        $item->setProduct($product);

        $item
            ->expects($this->once())
            ->method('getDefaultShipping')
            ->will($this->returnValue($shipping));

        $purchase->addPurchaseItem($store, $item);

        $purchase->updateShippingItems();

        $shippingItem = $item->getShippingItem();

        $this->assertInstanceOf('CL\Shipping\ShippingItem', $shippingItem);
        $this->assertFalse($shippingItem->isVoid());
        $this->assertEquals(10, $shippingItem->quantity);

        $this->assertSame($shipping, $shippingItem->getShipping());
        $this->assertTrue($purchase->getItems()->has($shippingItem));
        $this->assertSame($store, $purchase->getStorePurchases()->getFirst()->getStore());
        $this->assertTrue($purchase->getStorePurchases()->getFirst()->getItems()->has($shippingItem));
    }

    /**
     * @covers ::updateShippingItems
     */
    public function testUpdateShippingItemsStale()
    {
        $purchase = new Purchase();
        $store = new Store();
        $shipping = new Shipping();

        $item = $this->getMock('CL\Shipping\Test\Model\ProductItem', ['getDefaultShipping'], [['quantity' => 3]]);

        $item
            ->expects($this->once())
            ->method('getDefaultShipping')
            ->will($this->returnValue($shipping));

        $shippingItem = $this->getMock('CL\Shipping\ShippingItem', ['isStale'], [['quantity' => 4]]);
        $item->setShippingItem($shippingItem);

        $shippingItem
            ->expects($this->once())
            ->method('isStale')
            ->will($this->returnValue(true));

        $purchase->addPurchaseItem($store, $item);

        $purchase->updateShippingItems();

        $this->assertEquals(3, $shippingItem->quantity);
        $this->assertSame($shipping, $shippingItem->getShipping());
    }

    /**
     * @covers ::getShipping
     * @covers ::setShipping
     */
    public function testShipping()
    {
        $purchase = new Purchase();

        $shipping = $purchase->getShipping();

        $this->assertInstanceOf('CL\Purchases\Address', $shipping);
        $this->assertTrue($shipping->isVoid());

        $shipping = new Address();

        $purchase->setShipping($shipping);

        $this->assertSame($shipping, $purchase->getShipping());
    }
}
