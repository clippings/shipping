<?php

namespace CL\Shipping\Test;

use CL\Shipping\ShippingMethod;
use CL\Shipping\ShippingProfile;
use CL\Shipping\Test\Model\Store;

/**
 * @coversDefaultClass CL\Shipping\ShippableStoreTrait
 *
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class ShippableStoreTraitTest extends AbstractTestCase
{
    /**
     * @covers ::initialize
     */
    public function testInitialize()
    {
        $store = Store::getRepo();

        $shippingProfiles = $store->getRelOrError('shippingProfiles');
        $this->assertEquals('CL\Shipping\ShippingProfile', $shippingProfiles->getRepo()->getModelClass());

        $shippingMethods = $store->getRelOrError('shippingMethods');
        $this->assertEquals('CL\Shipping\ShippingMethod', $shippingMethods->getRepo()->getModelClass());
    }

    /**
     * @covers ::getShippingProfiles
     */
    public function testGetShippingProfiles()
    {
        $store = new Store();

        $profile = $store->getShippingProfiles()->getFirst();

        $this->assertInstanceOf('CL\Shipping\ShippingProfile', $profile);
    }

    /**
     * @covers ::getShippingMethods
     */
    public function testGetShippingMethods()
    {
        $store = new Store();

        $method = $store->getShippingMethods()->getFirst();

        $this->assertInstanceOf('CL\Shipping\ShippingMethod', $method);
    }
}
