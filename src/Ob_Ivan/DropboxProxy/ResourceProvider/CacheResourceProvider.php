<?php
/**
 * Provides resource to access memcache server.
 *
 * Parameters:
 *  - [cache.driver]
 *      Type of cache engine to use. Possible options:
 *          'none'      No caching provided at all. Calling set() does nothing.
 *          'memory'    Runtime memory cache.
 *          'memcache'  Connects to a single memcache server.
 *          'files'     Writes and reads files on local hard drive. Not implemented.
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
 *
 * Services:
 *  - [cache] instance of CacheInterface
**/
namespace Ob_Ivan\DropboxProxy\ResourceProvider;

use Ob_Ivan\Cache\Driver\MemcacheDriver;
use Ob_Ivan\Cache\CacheCollection;
use Ob_Ivan\ResourceContainer\ResourceContainer;
use Ob_Ivan\ResourceContainer\ResourceProviderInterface;

class CacheResourceProvider implements ResourceProviderInterface
{
    const DRIVER_NONE       = 'none';
    const DRIVER_MEMORY     = 'memory';
    const DRIVER_MEMCACHE   = 'memcache';
    const DRIVER_FILES      = 'files';
    const DRIVER_DB         = 'db';

    function populate(ResourceContainer $container)
    {
        $container->register('cache', function ($container) {

            // NEW
            $driverType = isset($container['cache.driver'])
                ? $container['cache.driver']
                : static::DRIVER_NONE;

            switch ($driverType) {
                case static::DRIVER_NONE:
                    break;

                case static::DRIVER_MEMORY:
                    break;

                case static::DRIVER_MEMCACHE:
                    break;

                case static::DRIVER_FILES:
                    throw new Exception('Cache driver type "' . $driverType . '" is not implemented yet');

                case static::DRIVER_DB:
                    throw new Exception('Cache driver type "' . $driverType . '" is not implemented yet');

                default:
                    throw new Exception('Unknown cache driver type "' . $driverType . '"');
            }

            // OLD: memcache
            $params = [];
            if (isset($container['memcache.unix_socket'])) {
                $params['host'] = 'unix://' . $container['memcache.unix_socket'];
                $params['port'] = 0;
            } elseif (isset($container['memcache.host']) && isset($container['memcache.port'])) {
                $params['host'] = $container['memcache.host'];
                $params['port'] = $container['memcache.port'];
            }
            return new CacheCollection(
                new MemcacheDriver($params),
                $container['memcache.namespace']
            );
        });
    }
}
