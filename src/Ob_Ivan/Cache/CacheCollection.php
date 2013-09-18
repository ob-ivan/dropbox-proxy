<?php
namespace Ob_Ivan\Cache;

use Ob_Ivan\Cache\Driver\DriverInterface;

class CacheCollection
{
    const NAMESPACE_SEPARATOR = '/';

    protected $driver;
    protected $prefix;

    protected $collections  = [];
    protected $elements     = [];

    public function __construct(DriverInterface $driver, $prefix = '')
    {
        $this->driver = $driver;
        $this->prefix = $prefix;
    }

    public function collection($namespace)
    {
        if (! isset($this->collections[$namespace])) {
            $this->collections[$namespace] = new static(
                $this->driver,
                $this->getKey($namespace)
            );
        }
        return $this->collections[$namespace];
    }

    public function element($key, $duration = 0)
    {
        if (! isset($this->elements[$key])) {
            $this->elements[$key] = [];
        }
        if (! isset($this->elements[$key][$duration])) {
            $this->elements[$key][$duration] = new CacheElement(
                $this->driver,
                $this->getKey($namespace),
                $duration
            );
        }
        return $this->elements[$key][$duration];
    }

    // protected //

    public function getKey($key)
    {
        return implode(static::NAMESPACE_SEPARATOR, [
            $this->prefix,
            $key
        ]);
    }
}
