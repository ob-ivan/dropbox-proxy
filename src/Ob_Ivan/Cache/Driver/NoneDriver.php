<?php
namespace Ob_Ivan\Cache\Driver;

use Ob_Ivan\Cache\StorageInterface;

class NoneDriver implements StorageInterface
{
    public function delete($key)
    {
        return true;
    }

    public function get($key)
    {
        return null;
    }

    public function set($key, $value, $expiry = null)
    {
        return true;
    }
}
