<?php
namespace Ob_Ivan\Cache;

interface StorageInterface
{
    public function delete($key);

    public function get($key);

    public function set($key, $value, $duration);
}
