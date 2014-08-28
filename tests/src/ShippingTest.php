<?php

namespace CL\Shipping\Test;

use CL\Shipping\Shipping;
use CL\Shipping\ShippingProfile;
use Harp\Range\Range;
use Harp\Locations\Location;
use SebastianBergmann\Money\Currency;
use SebastianBergmann\Money\Money;

/**
 * @coversDefaultClass CL\Shipping\Shipping
 *
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class ShippingTest extends AbstractTestCase
{
    /**
     * @covers ::initialize
     * @covers ::hasLocation
     * @covers ::getPriority
     */
    public function testInitialize()
    {
        $repo = Shipping::getRepo();

        $shippingProfile = $repo->getRelOrError('shippingProfile');
        $this->assertEquals('CL\Shipping\ShippingProfile', $shippingProfile->getRepo()->getModelClass());

        $this->assertTrue($repo->getInherited());
        $this->assertTrue($repo->getSoftDelete());

        $shipping = new Shipping();
        $this->assertEquals(new Range(0, 0), $shipping->getDays());
        $this->assertEquals(new Money(0, new Currency('GBP')), $shipping->getValue());
        $this->assertEquals(0, $shipping->getPriority());
        $this->assertEquals(false, $shipping->hasLocation(new Location));
    }

    /**
     * @covers ::getShippingProfile
     * @covers ::setShippingProfile
     */
    public function testStore()
    {
        $shipping = new Shipping();

        $shippingProfile = $shipping->getShippingProfile();

        $this->assertInstanceOf('CL\Shipping\ShippingProfile', $shippingProfile);
        $this->assertTrue($shippingProfile->isVoid());

        $shippingProfile = new ShippingProfile();

        $shipping->setShippingProfile($shippingProfile);

        $this->assertSame($shippingProfile, $shipping->getShippingProfile());
    }

    /**
     * @covers ::getDeliveryDays
     */
    public function testGetDeliveryDays()
    {
        $shipping = new Shipping(['days' => '3|5']);

        $shipping->setShippingProfile(new ShippingProfile(['days' => '5|15']));

        $this->assertEquals(new Range(8, 20), $shipping->getDeliveryDays());
    }

    /**
     * @covers ::getCurrency
     */
    public function testGetCurrency()
    {
        $shipping = new Shipping();

        $profile = $this->getMock('CL\Shipping\ShippingProfile', ['getCurrency']);
        $currency = new Currency('GBP');

        $profile
            ->expects($this->once())
            ->method('getCurrency')
            ->will($this->returnValue($currency));

        $shipping->setShippingProfile($profile);

        $this->assertSame($currency, $shipping->getCurrency());
    }
}
