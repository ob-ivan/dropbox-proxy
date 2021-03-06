<?php
/**
 * Provides resource to access memcache server.
 *
 * Parameters:
 *  - [cache.driver]
 *      Type of cache engine to use. Possible options:
 *          'memory'    Runtime in-memory cache.
 *          'memcache'  Connects to a single memcache server.
 *          'files'     Writes and reads files on local hard drive.
 *          'db'        Connects to database. Not implemented.
 *
 *  - [cache.namespace]
 *      Key prefix used to protect different applications using same cache
 *      service from key collision.
 *
 * Driver-specific parameters:
 *  - [cache.memcache.host]
 *  - [cache.memcache.port]
 *  - [cache.memcache.unix_socket]
 *  - [cache.files.folder]
 *
 * Services:
 *  - [cache] instance of Ob_Ivan\Cache\CacheCollection
**/
namespace Ob_Ivan\DropboxProxy\ResourceProvider;

use Ob_Ivan\Cache\Driver\FileDriver;
use Ob_Ivan\Cache\Driver\MemcacheDriver;
use Ob_Ivan\Cache\Driver\MemoryDriver;
use Ob_Ivan\Cache\CacheCollection;
use Ob_Ivan\ResourceContainer\ResourceContainer;
use Ob_Ivan\ResourceContainer\ResourceProviderInterface;

class CacheResourceProvider implements ResourceProviderInterface
{
    const DRIVER_MEMORY     = 'memory';
    const DRIVER_MEMCACHE   = 'memcache';
    const DRIVER_FILES      = 'files';
    const DRIVER_DB         = 'db';

    public function populate(ResourceContainer $container)
    {
        $container->register('cache', function ($container) {
            $driverType = isset($container['cache.driver'])
                ? $container['cache.driver']
                : static::DRIVER_MEMORY;

            return new CacheCollection(
                $this->getDriver($driverType, $container),
                isset($container['cache.namespace'])
                    ? $container['cache.namespace']
                    : ''
            );
        });
    }

    // protected //

    protected function getDriver($driverType, $container)
    {
        switch ($driverType) {
            case static::DRIVER_MEMORY:
                return new MemoryDriver();

            case static::DRIVER_MEMCACHE:
                $params = [];
                if (isset($container['cache.memcache.unix_socket'])) {
                    $params['host'] = 'unix://' . $container['cache.memcache.unix_socket'];
                    $params['port'] = 0;
                } elseif (isset($container['cache.memcache.host']) && isset($container['cache.memcache.port'])) {
                    $params['host'] = $container['cache.memcache.host'];
                    $params['port'] = $container['cache.memcache.port'];
                } else {
                    throw new Exception('Could not find config parameters for memcache');
                }
                return new MemcacheDriver($params);

            case static::DRIVER_FILES:
                $params = [];
                if (isset($container['cache.files.folder'])) {
                    $params['cache_dir'] = $container['cache.files.folder'];
                } else {
                    throw new Exception('Could not find config parameters for file cache');
                }
                return new FileDriver($params);

            case static::DRIVER_DB:
                throw new Exception('Cache driver type "' . $driverType . '" is not implemented yet');

            default:
                throw new Exception('Unknown cache driver type "' . $driverType . '"');
        }
    }
}
