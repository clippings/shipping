<?php

namespace CL\Shipping\Test;

use CL\Shipping\ShippingMethod;
use CL\Shipping\Test\Model\Store;

/**
 * @coversDefaultClass CL\Shipping\ShippingMethod
 *
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class ShippingMethodTest extends AbstractTestCase
{
    /**
     * @covers ::initialize
     */
    public function testInitialize()
    {
        $method = ShippingMethod::getRepo();

        $store = $method->getRelOrError('store');
        $this->assertEquals('CL\Shipping\Test\Model\Store', $store->getRepo()->getModelClass());
    }

    /**
     * @covers ::validate
     */
    public function testValidation()
    {
        $method = new ShippingMethod();

        $this->assertFalse($method->validate());

        $this->assertEquals('name must be present', $method->getErrors()->humanize());
    }

    /**
     * @covers ::getShippings
     */
    public function testGetShippings()
    {
        $method = new ShippingMethod();

        $shipping = $method->getShippings()->getFirst();

        $this->assertInstanceOf('CL\Shipping\Shipping', $shipping);
    }

    /**
     * @covers ::getStore
     * @covers ::setStore
     */
    public function testStore()
    {
        $method = new ShippingMethod();

        $store = $method->getStore();

        $this->assertInstanceOf('CL\Shipping\Test\Model\Store', $store);
        $this->assertTrue($store->isVoid());

        $store = new Store();

        $method->setStore($store);

        $this->assertSame($store, $method->getStore());
    }
}
