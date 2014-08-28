<?php

namespace CL\Shipping;

use Harp\Harp\AbstractModel;
use Harp\Harp\Config;
use Harp\Harp\Rel;
use Harp\Harp\Model\SoftDeleteTrait;
use Harp\Validate\Assert;
use CL\Purchases\Store;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class ShippingMethod extends AbstractModel
{
    use SoftDeleteTrait;

    public static function initialize(Config $config)
    {
        SoftDeleteTrait::initialize($config);

        $config
            ->addRels([
                new Rel\BelongsTo('store', $config, Store::getRepo()),
                new Rel\HasMany('shippings', $config, ShippingManual::getRepo()),
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
     * @return \Harp\Harp\Repo\LinkMany
     */
    public function getShippings()
    {
        return $this->all('shippings');
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
}
