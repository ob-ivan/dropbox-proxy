<?php
namespace Ob_Ivan\Cache\Driver;

use DateTime;
use Ob_Ivan\Cache\StorageInterface;

class MemoryDriver implements StorageInterface
{
    use ExpiryTrait;

    const KEY_EXPIRY = __LINE__;
    const KEY_VALUE  = __LINE__;

    /**
     *  @var [
     *      <string key> => [
     *          KEY_EXPIRY  => <DateTime Expiration date>,
     *          KEY_VALUE   => <mixed    Stored value>,
     *      ],
     *      ...
     *  ]
    **/
    protected $storage = [];

    public function delete($key)
    {
        unset($this->storage[$key]);
        return true;
    }

    public function get($key)
    {
        if (isset($this->storage[$key])) {
            if ($this->isExpired($this->storage[$key][static::KEY_EXPIRY])) {
                unset($this->storage[$key]);
            } else {
                return $this->storage[$key][static::KEY_VALUE];
            }
        }
        return null;
    }

    public function set($key, $value, $expiry = null)
    {
        if ($this->isExpired($expiry)) {
            return false;
        }
        $this->storage[$key] = [
            static::KEY_EXPIRY  => $this->normalizeExpiry($expiry),
            static::KEY_VALUE   => $value,
        ];
        return true;
    }
}
