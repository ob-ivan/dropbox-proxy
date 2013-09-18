<?php
namespace Ob_Ivan\Cache\Driver;

use Memcache;

class MemcacheDriver implements DriverInterface
{
    const MAX_KEY_LENGTH   = 100;
    const NORMALIZE_PREFIX = 'n';

    protected $memcache;
    protected $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function delete($key)
    {
        return $this->getMemcache()->delete($this->normalizeKey($key));
    }

    public function get($key)
    {
        return $this->getMemcache()->get($this->normalizeKey($key));
    }

    public function set($key, $value, $duration)
    {
        return $this->getMemcache()->set($this->normalizeKey($key), $value, 0, $duration);
    }

    // protected //

    protected function getMemcache()
    {
        if (! $this->memcache) {
            $this->memcache = new Memcache();
            $this->memcache->addServer(
                $this->params['host'],
                $this->params['port']
            );
        }
        return $this->memcache;
    }

    protected function normalizeKey($key)
    {
        if (strlen($key) > static::MAX_KEY_LENGTH) {
            $key = static::NORMALIZE_PREFIX . md5($key);
        }
        return $key;
    }
}
