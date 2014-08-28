<?php

namespace CL\Shipping\Test;

use CL\Shipping\Test\Model\ProductItem;
use CL\Shipping\Test\Model\Product;
use CL\Shipping\Test\Model\Store;
use CL\Shipping\Test\Model\Purchase;
use CL\Shipping\ShippingProfile;
use CL\Shipping\ShippingMethod;
use CL\Shipping\ShippingManual;
use CL\Shipping\Shipping;
use Harp\Locations\Location;
use Harp\Range\Range;
use SebastianBergmann\Money\Money;
use SebastianBergmann\Money\Currency;

/**
 * @coversNothing
 *
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class IntegrationTest extends AbstractTestCase
{
    public function testReadmeUsageExample()
    {
        $store = Store::find(1);
        $sofia = Location::findByName('Sofia');
        $bg = Location::findByName('Bulgaria');
        $post = ShippingMethod::findByName('Post');

        // Set up shipping for the product
        $manual_shipping = new ShippingManual(['value' => 300, 'days' => '2|5']);
        $manual_shipping->setShippingMethod($post);
        $manual_shipping->setLocation($bg);

        // Each shipping profile can have multiple "shippings" for different locations, methods or types
        // You can extend the Shipping class with your own types as well
        // Shipping profiles have processing time measured in days which get added to all shipping times
        $shippingProfile = new ShippingProfile(['currency' => 'EUR', 'name' => 'By Post', 'days' => '1|2']);
        $shippingProfile->getShippings()->add($manual_shipping);

        // Set up the product
        $product = new Product(['name' => 'test', 'value' => 1000, 'currency' => 'EUR']);
        $product->setShippingProfile($shippingProfile);

        // Persist the product and all the shipping data
        Product::save($product);

        $purchase = new Purchase();
        $purchase->addProduct($product, 2);

        // We set the shipping location so that the shipping can be properly calculated
        $purchase->setShippingLocation($sofia);

        // This will add ShippingItem object to the purchase.
        $purchase->updateShippingItems();

        // Each ProductItem will have a corresponding shippingitem
        $productItem = $purchase->getItems()->getFirst();

        $this->assertTrue($productItem->getShippingItem()->isPending());
        $this->assertEquals(2, $productItem->getShippingItem()->quantity);
        $this->assertSame($manual_shipping, $productItem->getShippingItem()->getShipping());

        $this->assertEquals(new Range(3, 7), $productItem->getShippingItem()->getDeliveryDays());
        $this->assertEquals(new Money(600, new Currency('GBP')), $productItem->getShippingItem()->getTotalValue());
    }
}
