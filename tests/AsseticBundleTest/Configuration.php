<?php
namespace AsseticBundleTest;

use AsseticBundle;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-11-17 at 11:53:23.
 */
class Configuration extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AsseticBundle\Configuration
     */
    protected $object;

    protected $testConfig = array();

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new AsseticBundle\Configuration($this->testConfig);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {}
}