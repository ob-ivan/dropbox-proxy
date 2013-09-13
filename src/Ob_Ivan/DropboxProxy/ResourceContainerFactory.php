<?php
namespace Ob_Ivan\DropboxProxy;

use Ob_Ivan\DropboxProxy\ResourceProvider\DropboxResourceProvider;
use Ob_Ivan\ResourceContainer\ResourceContainer;

class ResourceContainerFactory
{
    // TODO: Replace $storagePath argument with a substitution array.
    public static function getResourceContainer($configPath, $storagePath = null)
    {
        $config = json_decode(file_get_contents($configPath), true);
        $container = new ResourceContainer();
        $container->importProvider(new DropboxResourceProvider());
        $container->importValues($config);
        if ($storagePath) {
            $container['filesystem.storage'] = $storagePath;
        }
        return $container;
    }
}
