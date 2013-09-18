<?php
/**
 * Provides resource to access memcache server.
 *
 * Parameters:
 *  - [memcache.host]
 *  - [memcache.port]
 *  - [memcache.unix_socket]
 *  - [memcache.namespace]
 *
 * Services:
 *  - [memcache] instance of CacheCollection
**/
namespace Ob_Ivan\DropboxProxy\ResourceProvider;

use Ob_Ivan\Cache\Driver\MemcacheDriver;
use Ob_Ivan\Cache\CacheCollection;
use Ob_Ivan\ResourceContainer\ResourceContainer;
use Ob_Ivan\ResourceContainer\ResourceProviderInterface;

class MemcacheResourceProvider implements ResourceProviderInterface
{
    function populate(ResourceContainer $container)
    {
        $container->register('memcache', function ($container) {
            $params = [];
            if (isset($container['memcache.unix_socket'])) {
                $params['host'] = 'unix://' . $container['memcache.unix_socket'];
                $params['port'] = 0;
            } elseif (isset($container['memcache.host']) && isset($container['memcache.port'])) {
                $params['host'] = $container['memcache.host'];
                $params['port'] = $container['memcache.port'];
            } else {
                throw new Exception('No server parameters provided for memcache resource.');
            }
            return new CacheCollection(
                new MemcacheDriver($params),
                isset($container['memcache.namespace'])
                    ? $container['memcache.namespace']
                    : ''
            );
        });
    }
}
