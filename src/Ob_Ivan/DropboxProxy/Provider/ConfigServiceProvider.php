<?php
/**
 * Config file reader.
 *
 * Config is a json file with the following structure.
 *  {
 *      accessToken : String identifying client requests.
 *      code        : Authorization code that can be used once to obtain accessToken.
 *      debug       : Boolean which should be true only in development environment.
 *      storage     : (temporary) Path to the distributed files' directory relative to the document root.
 *  }
 *
 * This provider defines:
 *  [config]        PHP array that contains data from the file.
 *
 * This provides requires parameters:
 *  [config.path]   Path to the config file.
**/
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
                'Config service requires [config.path] parameter to be defined'
            );
        }
        $configContent = file_get_contents($app['config.path']);
        $app['config'] = json_decode($configContent, true);
    }
}
