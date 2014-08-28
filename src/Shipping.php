<?php

namespace CL\Shipping;

use Harp\Harp\AbstractModel;
use Harp\Harp\Rel;
use Harp\Harp\Config;
use Harp\Harp\Model\SoftDeleteTrait;
use Harp\Harp\Model\InheritedTrait;
use Harp\Locations\Location;
use Harp\Range\DaysRangeTrait;
use Harp\Money\ValueTrait;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class Shipping extends AbstractModel
{
    use SoftDeleteTrait;
    use DaysRangeTrait;
    use ValueTrait;
    use InheritedTrait;

    public static function initialize(Config $config)
    {
        InheritedTrait::initialize($config);
        SoftDeleteTrait::initialize($config);
        DaysRangeTrait::initialize($config);
        ValueTrait::initialize($config);

        $config
            ->addRels([
                new Rel\BelongsTo('shippingProfile', $config, ShippingProfile::getRepo()),
            ]);
    }

    /**
     * @var integer
     */
    public $id;

    /**
     * @var integer
     */
    public $shippingProfileId;

    /**
     * Check if a location is a vailable for this shipping
     *
     * @param  Location $location
     * @return boolean
     */
    public function hasLocation(Location $location)
    {
        return false;
    }

    /**
     * @return integer
     */
    public function getPriority()
    {
        return 0;
    }

    /**
     * @return ShippingProfile
     */
    public function getShippingProfile()
    {
        return $this->get('shippingProfile');
    }

    /**
     * @param  ShippingProfile $shippingProfile
     * @return static
     */
    public function setShippingProfile(ShippingProfile $shippingProfile)
    {
        return $this->set('shippingProfile', $shippingProfile);
    }

    /**
     * @return \SebastianBergmann\Money\Currency
     */
    public function getCurrency()
    {
        return $this->getShippingProfile()->getCurrency();
    }

    /**
     * @return \Harp\Range\Range
     */
    public function getDeliveryDays()
    {
        return $this->getDays()->add(
            $this->getShippingProfile()->getDays()
        );
    }
}
