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
        unset($storage[$key]);
        return true;
    }

    public function get($key)
    {
        if (isset($storage[$key])) {
            if ($storage[$key][static::KEY_EXPIRE] < (new DateTime)) {
                unset($storage[$key]);
            } else {
                return $storage[$key][static::KEY_VALUE];
            }
        }
        return null;
    }

    public function set($key, $value, $duration = null)
    {
        if ($duration < 0) {
            return false;
        }
        $storage[$key] = [
            static::KEY_EXPIRE  => new DateTime(
                $duration > 0
                    ? '+' . intval($duration) . 'sec'
                    : '+10000years'
            ),
            static::KEY_VALUE   => $value,
        ];
        return true;
    }
}
