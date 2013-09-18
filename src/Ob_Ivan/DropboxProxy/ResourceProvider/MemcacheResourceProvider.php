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
    }
}
