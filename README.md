Shipping
========

[![Build Status](https://travis-ci.org/clippings/shipping.png?branch=master)](https://travis-ci.org/clippings/shipping)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/clippings/shipping/badges/quality-score.png)](https://scrutinizer-ci.com/g/clippings/shipping/)
[![Code Coverage](https://scrutinizer-ci.com/g/clippings/shipping/badges/coverage.png)](https://scrutinizer-ci.com/g/clippings/shipping/)
[![Latest Stable Version](https://poser.pugx.org/clippings/shipping/v/stable.png)](https://packagist.org/packages/clippings/shipping)

Shipping extension for clippings/purchases package

Installing
----------

This package extends the [clippings/purchases](https://github.com/clippings/purchases) package, adding support for shipping calculations. In order to use it you'll need to extend several purchase models to add a trait to them. After that you'll need to set the dependancy container for harp to your own models:

```
Container::setActualClasses([
    'CL\Purchases\Product'     => 'MYOWN\Product',
    'CL\Purchases\Purchase'    => 'MYOWN\Purchase',
    'CL\Purchases\Store'       => 'MYOWN\Store',
    'CL\Purchases\ProductItem' => 'MYOWN\ProductItem',
]);

use CL\Shipping\ShippableProductTrait;

class Product extends \CL\Purchases\Product
{
    use ShippableProductTrait;

    public static function initialize(Config $config)
    {
        parent::initialize($config);

        ShippableProductTrait::initialize($config);
    }
}

use CL\Shipping\ShippablePurchaseTrait;

class Purchase extends \CL\Purchases\Purchase
{
    use ShippablePurchaseTrait;

    public static function initialize(Config $config)
    {
        parent::initialize($config);

        ShippablePurchaseTrait::initialize($config);
    }
}

use CL\Shipping\ShippableStoreTrait;

class Store extends \CL\Purchases\Store
{
    use ShippableStoreTrait;

    public static function initialize(Config $config)
    {
        parent::initialize($config);

        ShippableStoreTrait::initialize($config);
    }
}

use CL\Shipping\ShippableProductItemTrait;

class ProductItem extends \CL\Purchases\ProductItem
{
    use ShippableProductItemTrait;

    public static function initialize(Config $config)
    {
        parent::initialize($config);

        ShippableProductItemTrait::initialize($config);
    }
}
```

A diagram of the models
-----------------------

```
┌─────────────┐
│ Purchase    ├────────┐
└───┬─────────┘        │
    ↓                  ↓
┌─────────────┐   ┌─────────────────┐
│ ProductItem ├──→│ ShippingItem    ├───────────┐
└───┬─────────┘   └─────────────────┘           │
    ↓                                           ↓
┌─────────────┐   ┌─────────────────┐   ┌────────────────┐
│ Products    ├──→│ ShippingProfile ├──→│ Shipping       │
└───┬─────────┘   └─────────────────┘   ├────────────────┤
    │                                   │ ShippingManual │
    ↓                                   └────────────────┘
┌─────────────┐   ┌─────────────────┐           ↑
│ Store       ├──→│ StoreMethod     ├───────────┘
└─────────────┘   └─────────────────┘
```

Usage
-----
```
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
$purchase->addProduct($product);

// We set the shipping location so that the shipping can be properly calculated
$purchase->setShippingLocation($sofia);

// This will add ShippingItem object to the purchase.
$purchase->updateShippingItems();

// Each ProductItem will have a corresponding shippingitem
$productItem = $purchase->getItems()->getFirst();

var_dump($productItem->getShippingItem());
var_dump($productItem->getShippingItem()->getShipping());

// Value and delivery days are "freezable" values
var_dump($productItem->getShippingItem()->getDeliveryDays());
var_dump($productItem->getShippingItem()->getValue());
```


License
-------

Copyright (c) 2014, Clippings Ltd. Developed by Ivan Kerin

Under BSD-3-Clause license, read LICENSE file.
