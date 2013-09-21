<?php
namespace Ob_Ivan\Cache\Driver;

use Memcache;
use Ob_Ivan\Cache\StorageInterface;

class MemcacheDriver implements StorageInterface
{
    const MAX_KEY_LENGTH   = 100;
    const NORMALIZE_PREFIX = 'n';

    protected $memcache;
    protected $normalizedKeys = [];
    protected $params;

    /**
     *  @param  [
     *      'host'  => <string  Hostname, or 'unix://$pathToSocket' for unix-socket>,
     *      'port'  => <integer Port number, or 0 for unix-socket>,
     *  ]   $params
    **/
    public function __construct($params)
    {
        $this->params = $params;
    }

    // public : StorageInterface //

    public function delete($key)
    {
        return $this->getMemcache()->delete($this->normalizeKey($key));
    }

    public function get($key)
    {
        return $this->getMemcache()->get($this->normalizeKey($key));
    }

    public function set($key, $value, $duration = null)
    {
        return $this->getMemcache()->set($this->normalizeKey($key), $value, 0, intval($duration));
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
        if (! isset($this->normalizedKeys[$key])) {
            $this->normalizedKeys[$key] = strlen($key) > static::MAX_KEY_LENGTH
                ? static::NORMALIZE_PREFIX . md5($key)
                : $key;
        }
        return $this->normalizedKeys[$key];
    }
}
