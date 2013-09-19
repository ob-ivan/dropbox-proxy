<?php
namespace Ob_Ivan\DropboxProxy;

use Ob_Ivan\DropboxProxy\ResourceProvider\CacheResourceProvider;
use Ob_Ivan\DropboxProxy\ResourceProvider\DropboxResourceProvider;
use Ob_Ivan\ResourceContainer\ResourceContainer;

class ToolboxFactory
{
    /**
     * TODO: Replace $storagePath argument with a substitution array.
     *
     *  @param  string  $configPath
     *  @param  string  $storagePath    null
     *  @return ResourceContainer
    **/
    public static function getToolbox($configPath, $storagePath = null)
    {
        $config = json_decode(file_get_contents($configPath), true);
        $container = new ResourceContainer();
        $container->importProvider(new CacheResourceProvider());
        $container->importProvider(new DropboxResourceProvider());
        $container->importValues($config);
        if ($storagePath) {
            $container['filesystem.storage'] = $storagePath;
        }
        return $container;
    }
}
