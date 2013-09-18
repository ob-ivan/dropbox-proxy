<?php
namespace Ob_Ivan\Cache\Driver;

interface DriverInterface
{
    public function delete($key);

    public function get($key);

    public function set($key, $value, $duration);
}
