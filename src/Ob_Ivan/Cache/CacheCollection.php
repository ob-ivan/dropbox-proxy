<?php
namespace Ob_Ivan\Cache;

class CacheCollection implements StorageInterface
{
    const NAMESPACE_SEPARATOR = '/';

    protected $storage;
    protected $prefix;

    protected $collections  = [];
    protected $elements     = [];

    public function __construct(StorageInterface $storage, $prefix = '')
    {
        $this->storage = $storage;
        $this->prefix  = $prefix;
    }

    public function collection($namespace)
    {
        if (! isset($this->collections[$namespace])) {
            $this->collections[$namespace] = new static(
                $this->storage,
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
            /**
             * An element is a storage cell with preset key prefixed with
             * $this->prefix. Alternatively we could instantiate it as
             * new CacheElement($this, $key, $duration), but it will consume
             * more function calls per each access operation to calculate
             * resulting key.
            **/
            $this->elements[$key][$duration] = new CacheElement(
                $this->storage,
                $this->getKey($key),
                $duration
            );
        }
        return $this->elements[$key][$duration];
    }

    // public : StorageInterface //

    public function delete($key)
    {
        return $this->storage->delete($this->getKey($key));
    }

    public function get($key)
    {
        return $this->storage->get($this->getKey($key));
    }

    public function set($key, $value, $duration)
    {
        return $this->storage->set($this->getKey($key), $value, $duration);
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
