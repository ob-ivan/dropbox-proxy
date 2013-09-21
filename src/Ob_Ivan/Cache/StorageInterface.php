<?php
namespace Ob_Ivan\Cache;

interface StorageInterface
{
    public function delete($key);

    /**
     * Attempt to read a value associated with the specified key.
     *
     * If no value is stored under the specified key, implementation SHOULD
     * return null value. Implementation MAY return any other falsy value (like
     * boolean false or empty array), but it is NOT RECOMMENDED to do so.
     *
     * If lifetime for the accessed key is expired, implementation MUST treat
     * it the same way as if no value was stored.
     *
     *  @param  string  $key
     *  @return mixed|null
    **/
    public function get($key);

    /**
     * Store a value under specified key, optionally limiting its lifetime.
     *
     * Expiry date can be specified in one of several ways:
     *  - Null or integer zero means never expire.
     *  - Positive integer values are interpreted as number of seconds
     *    for the value to live.
     *  - DateTime object denotes exact date and time since when the value
     *    will be considered expired and eventually pruned from the storage.
     *
     *  @param  string  $key
     *  @param  mixed   $value
     *  @param  integer $expiry     null
     *  @return boolean                     Whether operation was successful.
    **/
    public function set($key, $value, $expiry = null);
}
