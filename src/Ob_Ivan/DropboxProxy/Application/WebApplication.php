<?php
/**
 * An application handling web requests.
 *
 * Basic routes and controllers are defined here, but some parameters are
 * required for this to work.
 *
 * Usage:
 *
 *  $app = new WebApplication([
 *      'config.path'               => <Path to the config file. See ConfigServiceProvider for details>,
 *      'dropbox.auth_info.json'    => <Path to json file with your app's credentials>,
 *  ]);
**/
namespace Ob_Ivan\DropboxProxy\Application;

use Ob_Ivan\DropboxProxy\Application\RichApplication as WrappedApplication;
use Ob_Ivan\DropboxProxy\Provider\DropboxServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;

class WebApplication
{
    /**
     * @var WrappedApplication
    **/
    private $app;

    public function __construct($configPath)
    {
        // Locate config at $configPath and read it.
        // TODO: Elaborate on this, add substitutions interpolation etc.
        // TODO: Eliminate code mirroring with ConsoleApplication::__construct.
        $config = json_decode(file_get_contents($configPath), true);

        // Create an app and pass config values into it.
        $app = $this->app = new WrappedApplication($config);

        // Register providers.
        // TODO: Take out provider list to a method that can be overriden in child classes.
        $app->register(new DropboxServiceProvider());
        $app->register(new SessionServiceProvider());
        $app->register(new UrlGeneratorServiceProvider());

        // Set a bridge between [dropbox] and [config] services.
        $accessTokenFactory = $app->raw('dropbox.access_token');
        $app['dropbox.access_token'] = $app->share(function () use ($app, $accessTokenFactory) {
            if (isset($app['config']['accessToken'])) {
                return $app['config']['accessToken'];
            }
            return $accessTokenFactory($app);
        });
        $app['dropbox.auth_code'] = $app->share(function () use ($app) {
            if (isset($app['config']['authCode'])) {
                return $app['config']['authCode'];
            }
        });

        // Routing and controllers //

        // Obtain access token with a two-step authorization.
        $app->get('/dropbox-auth-start', function () use ($app) {
            // Redirect to Dropbox page and generate an authorization code.
            return $app->redirect($app['dropbox.web_auth']->start());
        })->bind('dropbox-auth-start');

        $app->get('/dropbox-auth-finish', function () use ($app) {
            return $app['dropbox.access_token'];
        })->bind('dropbox-auth-finish');

        // Download a file from filesystem storage.
        $app->get('/{file}', function ($file) use ($app) {
            $filename = implode(DIRECTORY_SEPARATOR, [$app['docroot'], $app['config']['storage'], $file]);
            if (! file_exists($filename)) {
                return
                    'File "' . $file . '" was not found on server. ' .
                    'Check for typos or contact system administrator.'
                ;
            }
            return $app->sendFile($filename);
        });

        // List available files.
        $app->get('/', function () use ($app) {

            $folderMetadata = $app['dropbox.client']->getMetadataWithChildren($app['config']['root']);
            $contents = $folderMetadata['contents'];
            return '<pre>' . print_r($contents, true) . '</pre>'; // debug

            // TODO: folder listing
            return 'Folder listing is not yet supported. Please come back later.';
        });
    }

    public function run()
    {
        $this->app->run();
    }
}
