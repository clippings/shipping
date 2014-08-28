<?php

namespace CL\Shipping;

use Harp\Harp\Config;
use Harp\Harp\Rel;
use Harp\Locations\Location;
use Harp\Validate\Assert;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class ShippingManual extends Shipping
{
    public static function initialize(Config $config)
    {
        parent::initialize($config);

        $config
            ->addRels([
                new Rel\BelongsTo('shippingMethod', $config, ShippingMethod::getRepo()),
                new Rel\BelongsTo('location', $config, Location::getRepo()),
            ])
            ->addAsserts([
                new Assert\Present('days'),
            ]);
    }

    /**
     * @var integer
     */
    public $shippingMethodId;

    /**
     * @var integer
     */
    public $locationId;

    /**
     * Check if the location is contained by its own location
     *
     * @param  Location $location
     * @return boolean
     */
    public function hasLocation(Location $location)
    {
        return $this->isVoid() ? false : $this->getLocation()->contains($location);
    }

    /**
     * Get the priority.
     *
     * - More specific locations have more priority,
     * - If locations have the same specificity the lower price has more priority
     *
     * @return integer
     */
    public function getPriority()
    {
        $priority = $this->getLocation()->getDepth();

        if (($amount = $this->getValue()->getAmount())) {
            $priority += (0.9 / $amount);
        }

        return $priority;
    }

    /**
     * @return ShippingMethod
     */
    public function getShippingMethod()
    {
        return $this->get('shippingMethod');
    }

    /**
     * @param ShippingMethod $shippingMethod
     * @return self
     */
    public function setShippingMethod(ShippingMethod $shippingMethod)
    {
        $this->set('shippingMethod', $shippingMethod);

        return $this;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->get('location');
    }

    /**
     * @param Location $location
     * @return self
     */
    public function setLocation(Location $location)
    {
        $this->set('location', $location);

        return $this;
    }
}
