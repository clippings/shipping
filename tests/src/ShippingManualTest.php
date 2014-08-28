<?php

namespace CL\Shipping\Test;

use CL\Shipping\ShippingManual;
use CL\Shipping\ShippingMethod;
use Harp\Range\Range;
use Harp\Locations\Location;
use SebastianBergmann\Money\Currency;
use SebastianBergmann\Money\Money;

/**
 * @coversDefaultClass CL\Shipping\ShippingManual
 *
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class ShippingManualTest extends AbstractTestCase
{
    /**
     * @covers ::initialize
     * @covers ::hasLocation
     * @covers ::getPriority
     */
    public function testInitialize()
    {
        $repo = ShippingManual::getRepo();

        $method = $repo->getRelOrError('shippingMethod');
        $this->assertEquals('CL\Shipping\ShippingMethod', $method->getRepo()->getModelClass());

        $location = $repo->getRelOrError('location');
        $this->assertEquals('Harp\Locations\Location', $location->getRepo()->getModelClass());
    }

    /**
     * @covers ::getShippingMethod
     * @covers ::setShippingMethod
     */
    public function testShippingMethod()
    {
        $shipping = new ShippingManual();

        $shippingMethod = $shipping->getShippingMethod();

        $this->assertInstanceOf('CL\Shipping\ShippingMethod', $shippingMethod);
        $this->assertTrue($shippingMethod->isVoid());

        $shippingMethod = new ShippingMethod();

        $shipping->setShippingMethod($shippingMethod);

        $this->assertSame($shippingMethod, $shipping->getShippingMethod());
    }

    /**
     * @covers ::getLocation
     * @covers ::setLocation
     */
    public function testLocation()
    {
        $shipping = new ShippingManual();

        $location = $shipping->getLocation();

        $this->assertInstanceOf('Harp\Locations\Location', $location);
        $this->assertTrue($location->isVoid());

        $location = new Location();

        $shipping->setLocation($location);

        $this->assertSame($location, $shipping->getLocation());
    }

    /**
     * @covers ::hasLocation
     */
    public function testHasLocation()
    {
        $shipping = new ShippingManual();
        $shipping->setLocation(Location::findByName('Bulgaria'));

        $this->assertTrue($shipping->hasLocation(Location::findByName('Sofia')));
        $this->assertTrue($shipping->hasLocation(Location::findByName('Bulgaria')));
        $this->assertFalse($shipping->hasLocation(Location::findByName('Everywhere')));
    }

    public function dataGetPriority()
    {
        return [
            [2000, 'Bulgaria', 1.00045],
            [4000, 'Bulgaria', 1.000225],
            [2000, 'Everywhere', 0.00045],
            [200, 'Sofia', 2.0045],
            [1, 'Sofia', 2.9],
            [1, null, 0.9],
            [0, null, 0],
        ];
    }

    /**
     * @dataProvider dataGetPriority
     * @covers ::getPriority
     */
    public function testGetPriority($amount, $locationName, $expected)
    {
        $shipping = new ShippingManual();
        $shipping->setValue(new Money($amount, new Currency('GBP')));
        $shipping->setLocation(Location::findByName($locationName));

        $this->assertEquals($expected, $shipping->getPriority());
    }
}
