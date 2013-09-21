<?php
namespace Ob_Ivan\Cache;

class CacheElement
{
    protected $storage;
    protected $key;
    protected $expiry;

    public function __construct(StorageInterface $storage, $key, $expiry = null)
    {
        $this->storage  = $storage;
        $this->key      = $key;
        $this->expiry   = $expiry;
    }

    public function delete()
    {
        return $this->storage->delete($this->key);
    }

    public function get()
    {
        return $this->storage->get($this->key);
    }

    public function set($value)
    {
        return $this->storage->set($this->key, $value, $this->expiry);
    }
}
