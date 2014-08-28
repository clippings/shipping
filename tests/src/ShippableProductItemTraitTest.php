<?php

namespace CL\Shipping\Test;

use CL\Shipping\Test\Model\ProductItem;
use CL\Shipping\Test\Model\Product;
use CL\Shipping\ShippingProfile;
use CL\Shipping\ShippingItem;
use CL\Shipping\Shipping;
use Harp\Locations\Location;

/**
 * @coversDefaultClass CL\Shipping\ShippableProductItemTrait
 *
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class ShippableProductItemTraitTest extends AbstractTestCase
{
    /**
     * @covers ::initialize
     */
    public function testInitialize()
    {
        $repo = ProductItem::getRepo();

        $shippingItem = $repo->getRelOrError('shippingItem');
        $this->assertEquals('CL\Shipping\ShippingItem', $shippingItem->getRepo()->getModelClass());
    }

    /**
     * @covers ::getShippingItem
     * @covers ::setShippingItem
     */
    public function testShippingItem()
    {
        $productItem = new ProductItem();

        $shippingItem = $productItem->getShippingItem();

        $this->assertInstanceOf('CL\Shipping\ShippingItem', $shippingItem);
        $this->assertTrue($shippingItem->isVoid());

        $shippingItem = new ShippingItem();

        $productItem->setShippingItem($shippingItem);

        $this->assertSame($shippingItem, $productItem->getShippingItem());
    }

    public function testGetDefaultShipping()
    {
        $productItem = new ProductItem();
        $location = new Location();
        $product = new Product();
        $shippingProfile = new ShippingProfile();
        $shipping = new Shipping();

        $purchase = $this->getMock('CL\Shipping\Test\Model\Purchase', ['getShippingLocation']);
        $shippingProfile = $this->getMock('CL\Shipping\ShippingProfile', ['getDefaultShippingFor']);

        $product->setShippingProfile($shippingProfile);
        $productItem
            ->setProduct($product)
            ->setPurchase($purchase);

        $purchase
            ->expects($this->once())
            ->method('getShippingLocation')
            ->will($this->returnValue($location));

        $shippingProfile
            ->expects($this->once())
            ->method('getDefaultShippingFor')
            ->with($this->identicalTo($location))
            ->will($this->returnValue($shipping));

        $this->assertSame($shipping, $productItem->getDefaultShipping());
    }

}
