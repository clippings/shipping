<?php

namespace CL\Shipping;

use Harp\Harp\AbstractModel;
use Harp\Harp\Config;
use Harp\Harp\Rel;
use Harp\Validate\Assert;
use Harp\Harp\Model\SoftDeleteTrait;
use Harp\Range\DaysRangeTrait;
use Harp\Money\CurrencyTrait;
use Harp\Locations\Location;
use CL\Purchases\Product;
use CL\Purchases\Store;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class ShippingProfile extends AbstractModel
{
    use SoftDeleteTrait;
    use DaysRangeTrait;
    use CurrencyTrait;

    public static function initialize(Config $config)
    {
        SoftDeleteTrait::initialize($config);
        DaysRangeTrait::initialize($config);
        CurrencyTrait::initialize($config);

        $config
            ->addRels([
                new Rel\HasMany('products', $config, Product::getRepo(), [
                    'inverseOf' => 'shippingProfile'
                ]),
                new Rel\HasMany('shippings', $config, Shipping::getRepo(), [
                    'inverseOf' => 'shippingProfile'
                ]),
                new Rel\BelongsTo('store', $config, Store::getRepo(), [
                    'inverseOf' => 'shippingProfiles'
                ]),
                new Rel\BelongsTo('shipsFrom', $config, Location::getRepo()),
            ])
            ->addAsserts([
                new Assert\Present('name'),
            ]);
    }

    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var integer
     */
    public $storeId;

    /**
     * @param  Location                   $location
     * @return \Harp\Harp\Repo\RepoModels
     */
    public function getAvailableShippingsFor(Location $location)
    {
        return $this->getShippings()
            ->filter(function (Shipping $shipping) use ($location) {
                return $shipping->hasLocation($location);
            })
            ->sort(function (Shipping $shipping1, Shipping $shipping2) {
                return $shipping2->getPriority() - $shipping1->getPriority();
            });
    }

    /**
     * @param  Location $location
     * @return Shipping
     */
    public function getDefaultShippingFor(Location $location)
    {
        return $this->getAvailableShippingsFor($location)->getFirst();
    }

    /**
     * @return \Harp\Harp\Repo\LinkMany
     */
    public function getShippings()
    {
        return $this->all('shippings');
    }

    /**
     * @return \Harp\Harp\Repo\LinkMany
     */
    public function getProducts()
    {
        return $this->all('products');
    }

    /**
     * @return Store
     */
    public function getStore()
    {
        return $this->get('store');
    }

    /**
     * @param  Store $store
     * @return self
     */
    public function setStore(Store $store)
    {
        $this->set('store', $store);

        return $this;
    }

    /**
     * @return Location
     */
    public function getShipsFrom()
    {
        return $this->get('shipsFrom');
    }

    /**
     * @param  Location $shipsFrom
     * @return self
     */
    public function setShipsFrom(Location $shipsFrom)
    {
        $this->set('shipsFrom', $shipsFrom);

        return $this;
    }
}
