<?php

use Ob_Ivan\Cache\Driver\MemoryDriver;

class MemoryDriverTest extends PHPUnit_Framework_TestCase
{
    protected $driver;

    public function setUp()
    {
        $this->driver = new MemoryDriver();
    }

    // public : tests //

    public function testDelete()
    {
        $this->assertTrue(
            $this->driver->delete(__METHOD__),
            'delete() did not return success code'
        );
    }

    public function testGet()
    {
        $this->assertNull(
            $this->driver->get(__METHOD__),
            'get() did not return null on the first call'
        );
    }

    public function testSet()
    {
        $this->assertTrue(
            $this->driver->set(__METHOD__, __METHOD__),
            'set() without duration did not return success code'
        );
        $this->assertTrue(
            $this->driver->set(__METHOD__, __METHOD__, 1),
            'set() with positive duration did not return success code'
        );
        $this->assertFalse(
            $this->driver->set(__METHOD__, __METHOD__, -1),
            'set() with negative duration did not return fail code'
        );
    }

    public function testSetGet()
    {
        $this->driver->set(__METHOD__, __METHOD__);
        $this->assertEquals(
            __METHOD__,
            $this->driver->get(__METHOD__),
            'get() did not return the value, that was previously set()'
        );
    }

    public function testSetDeleteGet()
    {
        $this->driver->set(__METHOD__, __METHOD__);
        $this->driver->delete(__METHOD__);
        $this->assertNull(
            $this->driver->get(__METHOD__),
            'get() did not return the value, that was previously delete()d'
        );
    }

    public function testSetWaitGet()
    {
        $this->driver->set(__METHOD__, __METHOD__, 1);
        sleep(2);
        $this->assertNull(
            $this->driver->get(__METHOD__),
            'get() did not return null after value expiry'
        );
    }
}
