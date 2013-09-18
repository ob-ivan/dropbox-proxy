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
 *  - [memcache]
**/
namespace Ob_Ivan\DropboxProxy\ResourceProvider;

use Memcache;
use Ob_Ivan\ResourceContainer\ResourceContainer;
use Ob_Ivan\ResourceContainer\ResourceProviderInterface;

class MemcacheResourceProvider implements ResourceProviderInterface
{
    function populate(ResourceContainer $container)
    {
        $container->register('memcache', function ($container) {
            $memcache = new Memcache();
            if (isset($container['memcache.unix_socket'])) {
                $memcache->addServer(
                    'unix://' . $container['memcache.unix_socket'],
                    0
                );
            } elseif (isset($container['memcache.host']) && isset($container['memcache.port'])) {
                $memcache->addServer(
                    $container['memcache.host'],
                    $container['memcache.port']
                );
            }
            // TODO: Set namespace.
        });
    }
}
