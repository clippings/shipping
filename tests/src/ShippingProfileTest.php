<?php

namespace CL\Shipping\Test;

use CL\Shipping\ShippingProfile;
use CL\Shipping\Shipping;
use CL\Shipping\Test\Model\Store;
use Harp\Locations\Location;
use Harp\Harp\Model\State;
use Harp\Harp\Repo\RepoModels;

/**
 * @coversDefaultClass CL\Shipping\ShippingProfile
 *
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class ShippingProfileTest extends AbstractTestCase
{
    /**
     * @covers ::initialize
     * @covers ::getShippings
     * @covers ::getProducts
     */
    public function testInitialize()
    {
        $repo = ShippingProfile::getRepo();

        $store = $repo->getRelOrError('store');
        $this->assertEquals('CL\Shipping\Test\Model\Store', $store->getRepo()->getModelClass());

        $location = $repo->getRelOrError('shipsFrom');
        $this->assertEquals('Harp\Locations\Location', $location->getRepo()->getModelClass());

        $products = $repo->getRelOrError('products');
        $this->assertEquals('CL\Shipping\Test\Model\Product', $products->getRepo()->getModelClass());

        $shippings = $repo->getRelOrError('shippings');
        $this->assertEquals('CL\Shipping\Shipping', $shippings->getRepo()->getModelClass());

        $profile = new ShippingProfile();

        $shipping = $profile->getShippings()->getFirst();

        $this->assertInstanceOf('CL\Shipping\Shipping', $shipping);

        $product = $profile->getProducts()->getFirst();

        $this->assertInstanceOf('CL\Shipping\Test\Model\Product', $product);
    }

    /**
     * @covers ::validate
     */
    public function testValidation()
    {
        $profile = new ShippingProfile();

        $this->assertFalse($profile->validate());

        $this->assertEquals('name must be present', $profile->getErrors()->humanize());
    }

    /**
     * @covers ::getStore
     * @covers ::setStore
     */
    public function testStore()
    {
        $profile = new ShippingProfile();

        $store = $profile->getStore();

        $this->assertInstanceOf('CL\Shipping\Test\Model\Store', $store);
        $this->assertTrue($store->isVoid());

        $store = new Store();

        $profile->setStore($store);

        $this->assertSame($store, $profile->getStore());
    }

    /**
     * @covers ::getShipsFrom
     * @covers ::setShipsFrom
     */
    public function testShipsFrom()
    {
        $profile = new ShippingProfile();

        $location = $profile->getShipsFrom();

        $this->assertInstanceOf('Harp\Locations\Location', $location);
        $this->assertTrue($location->isVoid());

        $location = new Location();

        $profile->setShipsFrom($location);

        $this->assertSame($location, $profile->getShipsFrom());
    }

    public function testGetDefaultShippingFor()
    {
        $profile = $this->getMock('CL\Shipping\ShippingProfile', ['getAvailableShippingsFor']);

        $shipping1 = new Shipping();
        $shipping2 = new Shipping();
        $shipping3 = new Shipping();

        $shippings = [$shipping1, $shipping2, $shipping3];

        $location = new Location();

        $profile
            ->expects($this->exactly(3))
            ->method('getAvailableShippingsFor')
            ->with($this->identicalTo($location))
            ->will(
                $this->onConsecutiveCalls(
                    new RepoModels(Shipping::getRepo(), [$shipping1, $shipping2]),
                    new RepoModels(Shipping::getRepo(), [$shipping3, $shipping2, $shipping1]),
                    new RepoModels(Shipping::getRepo(), [])
                )
            );

        $this->assertSame($shipping1, $profile->getDefaultShippingFor($location));
        $this->assertSame($shipping3, $profile->getDefaultShippingFor($location));
        $this->assertEquals(new Shipping([], State::VOID), $profile->getDefaultShippingFor($location));
    }

    public function testGetAvailableShippingsFor()
    {
        $profile = $this->getMock('CL\Shipping\ShippingProfile', ['getShippings']);

        $location = new Location();

        $shipping1 = $this->getMock('CL\Shipping\Shipping', ['hasLocation', 'getPriority']);
        $shipping1
            ->expects($this->once())
            ->method('hasLocation')
            ->with($this->identicalTo($location))
            ->will($this->returnValue(true));

        $shipping1
            ->expects($this->atLeastOnce())
            ->method('getPriority')
            ->will($this->returnValue(1));

        $shipping2 = $this->getMock('CL\Shipping\Shipping', ['hasLocation']);
        $shipping2
            ->expects($this->once())
            ->method('hasLocation')
            ->with($this->identicalTo($location))
            ->will($this->returnValue(false));

        $shipping3 = $this->getMock('CL\Shipping\Shipping', ['hasLocation', 'getPriority']);
        $shipping3
            ->expects($this->once())
            ->method('hasLocation')
            ->with($this->identicalTo($location))
            ->will($this->returnValue(true));

        $shipping3
            ->expects($this->atLeastOnce())
            ->method('getPriority')
            ->will($this->returnValue(2));

        $shippings = [$shipping1, $shipping2, $shipping3];

        $profile
            ->expects($this->once())
            ->method('getShippings')
            ->will($this->returnValue(new RepoModels(Shipping::getRepo(), [$shipping1, $shipping2, $shipping3])));

        $result = @ $profile->getAvailableShippingsFor($location);

        $this->assertSame([$shipping3, $shipping1], $result->toArray($location));
    }
}
