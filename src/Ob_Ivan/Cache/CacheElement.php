<?php
namespace Ob_Ivan\Cache;

use Ob_Ivan\Cache\Driver\DriverInterface;

class CacheElement
{
    protected $driver;
    protected $key;
    protected $duration;

    public function __construct(DriverInterface $driver, $key, $duration = 0)
    {
        $this->driver   = $driver;
        $this->key      = $key;
        $this->duration = $duration;
    }

    public function delete()
    {
        return $this->driver->delete($this->key);
    }

    public function get()
    {
        return $this->driver->get($this->key);
    }

    public function set($value)
    {
        return $this->driver->set($this->key, $value, $this->duration);
    }
}
