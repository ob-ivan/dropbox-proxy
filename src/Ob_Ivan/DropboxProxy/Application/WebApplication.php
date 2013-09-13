<?php
/**
 * An application handling web requests.
 *
 * Basic routes and controllers are defined here, but config file is
 * required for this to work.
 *
 * Usage:
 *
 *  $app = new WebApplication(
 *      <string  Path to config.json>,
 *      [<string Path to local storage>]
 *  );
 *  $app->run();
**/
namespace Ob_Ivan\DropboxProxy\Application;

use Ob_Ivan\DropboxProxy\Application\RichApplication as WrappedApplication;
use Ob_Ivan\DropboxProxy\ResourceProvider\DropboxResourceProvider;
use Ob_Ivan\ResourceContainer\ResourceContainer;

class WebApplication
{
    /**
     * @var WrappedApplication
    **/
    private $app;

    /**
     *  @param  string  $configPath             Path to config.json.
     *  @param  string  $storagePath    null    Path to local storage.
    **/
    public function __construct($configPath, $storagePath = null)
    {
        // Locate config at $configPath and read it.
        // TODO: Elaborate on this, add substitutions interpolation etc.
        // TODO: Eliminate code mirroring with ConsoleApplication::__construct.
        $config = json_decode(file_get_contents($configPath), true);
        $container = new ResourceContainer();
        $container->importProvider(new DropboxResourceProvider());
        $container->importValues($config);
        $container['filesystem.storage'] = $storagePath;

        // Create an app and pass config values into it.
        $app = $this->app = new WrappedApplication();
        $app['container'] = $container;

        // Routing and controllers //

        // Obtain access token with a two-step authorization.
        $app->get('/dropbox-auth-start', function () use ($app) {
            // Redirect to Dropbox page and generate an authorization code.
            return $app->redirect($app['container']['dropbox.web_auth']->start());
        })->bind('dropbox-auth-start');

        $app->get('/dropbox-auth-finish', function () use ($app) {
            return $app['container']['dropbox.access_token'];
        })->bind('dropbox-auth-finish');

        // Download a file from filesystem storage.
        $app->get('/{file}', function ($file) use ($app) {
            $filename = implode(DIRECTORY_SEPARATOR, [
                $app['docroot'],
                $app['container']['filesystem.storage'],
                $file,
            ]);
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

            $folderMetadata = $app['container']['dropbox.client']
                ->getMetadataWithChildren($app['container']['dropbox.root']);
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
