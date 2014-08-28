<?php

namespace CL\Shipping\Test;

use Harp\Query\DB;
use Harp\Harp\Repo\Container;
use PHPUnit_Framework_TestCase;
use CL\Shipping\Test\Repo;
use CL\CurrencyConvert\Converter;
use CL\CurrencyConvert\NullSource;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
abstract class AbstractTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var TestLogger
     */
    protected $logger;

    /**
     * @return TestLogger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    public function setUp()
    {
        parent::setUp();

        $this->logger = new TestLogger();

        DB::setConfig([
            'dsn' => 'mysql:dbname=clippings/shipping;host=127.0.0.1',
            'username' => 'root',
        ]);

        DB::get()->setLogger($this->logger);
        DB::get()->beginTransaction();

        Container::clear();

        Converter::initialize(new NullSource());

        Container::setActualClasses([
            'CL\Purchases\Product'     => 'CL\Shipping\Test\Model\Product',
            'CL\Purchases\Purchase'    => 'CL\Shipping\Test\Model\Purchase',
            'CL\Purchases\Store'       => 'CL\Shipping\Test\Model\Store',
            'CL\Purchases\ProductItem' => 'CL\Shipping\Test\Model\ProductItem',
        ]);
    }

    public function tearDown()
    {
        DB::get()->rollback();

        parent::tearDown();
    }

    public function assertQueries(array $query)
    {
        $this->assertEquals($query, $this->getLogger()->getEntries());
    }
}
