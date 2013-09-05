<?php
namespace Ob_Ivan\DropboxProxy;

use Silex\Application;
use Silex\ServiceProviderInterface;

class ConfigServiceProvider implements ServiceProviderInterface
{
    function register(Application $app)
    {
    }

    function boot(Application $app)
    {
        if (! isset($app['config.path'])) {
            throw new Exception(
                'Config service requires config.path parameter to be defined'
            );
        }
        $configContent = file_get_contents($app['config.path']);
        $app['config'] = json_decode($configContent, true);
    }
}
