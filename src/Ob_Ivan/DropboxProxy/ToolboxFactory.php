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
    public function getToolbox($configPath, $storagePath = null)
    {
        $config = json_decode(file_get_contents($configPath), true);
        $container = new ResourceContainer();
        foreach ($this->getDefaultProviders() as $provider) {
            $container->importProvider($provider);
        }
        $container->importValues($config);
        if ($storagePath) {
            $container['filesystem.storage'] = $storagePath;
        }
        return $container;
    }

    /**
     * Return a list of provider instances to be imported when creating
     * new toolbox instance.
     *
     *  @return [ResourceProviderInterface]
    **/
    protected function getDefaultProviders()
    {
        return [
            new CacheResourceProvider,
            new DropboxResourceProvider,
        ];
    }
}
