<?php
namespace Ob_Ivan\Cache;

interface StorageInterface
{
    public function delete($key);

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
