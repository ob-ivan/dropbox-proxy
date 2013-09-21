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
     * Store a value under specified key for a duration no longer than specified.
     *
     *  @param  string  $key
     *  @param  mixed   $value
     *  @param  integer $duration   null    Maximal lifetime for value, in seconds.
     *                                      Null value stands for no expiry.
     *  @return boolean                     Whether operation was successful.
    **/
    public function set($key, $value, $duration = null);
}
