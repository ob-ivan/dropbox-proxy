<?php
namespace Ob_Ivan\Cache\Driver;

use DateTime;
use Ob_Ivan\Cache\StorageInterface;

class MemoryDriver implements StorageInterface
{
    const KEY_EXPIRE = __LINE__;
    const KEY_VALUE  = __LINE__;

    /**
     *  @var [
     *      <string key> => [
     *          KEY_EXPIRE  => <DateTime Expiration date>,
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
            if ($this->storage[$key][static::KEY_EXPIRE] < (new DateTime)) {
                unset($this->storage[$key]);
            } else {
                return $this->storage[$key][static::KEY_VALUE];
            }
        }
        return null;
    }

    public function set($key, $value, $duration = null)
    {
        if ($duration < 0) {
            return false;
        }
        $this->storage[$key] = [
            static::KEY_EXPIRE  => $this->getExpiry($duration),
            static::KEY_VALUE   => $value,
        ];
        return true;
    }

    // protected //

    protected function getExpiry($duration = null)
    {
        return new DateTime(
            $duration > 0
            ? '+' . intval($duration) . 'sec'
            : '+1000years'
        );
    }
}
