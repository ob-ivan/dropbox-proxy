<?php
/**
 * Config file reader.
 *
 * Config is a json file with the following structure.
 *  {
 *      accessToken : String identifying client requests.
 *      code        : Authorization code that can be used once to obtain accessToken.
 *      storage     : (temporary) Path to the distributed files' directory relative to the document root.
 *  }
 *
 * Parameters:
 *  [config.path]   (required) Path to the config file.
 *
 * Services:
 *  [config]        PHP array that contains data from the file.
 *
**/
namespace Ob_Ivan\DropboxProxy\Provider;

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
