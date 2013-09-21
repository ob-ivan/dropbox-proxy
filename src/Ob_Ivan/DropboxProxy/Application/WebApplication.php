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
use Ob_Ivan\DropboxProxy\ToolboxFactory;

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
        // Create an app and pass resource toolbox into it.
        $app = $this->app = new WrappedApplication();
        $app['toolbox'] = (new ToolboxFactory)->getToolbox($configPath, $storagePath);

        // Routing and controllers //

        // Obtain access token with a two-step authorization.
        $app->get('/dropbox-auth-start', function () use ($app) {
            // Redirect to Dropbox page and generate an authorization code.
            return $app->redirect($app['toolbox']['dropbox.web_auth']->start());
        })->bind('dropbox-auth-start');

        $app->get('/dropbox-auth-finish', function () use ($app) {
            return $app['toolbox']['dropbox.access_token'];
        })->bind('dropbox-auth-finish');

        // Download a file from filesystem storage.
        $app->get('/{file}', function ($file) use ($app) {
            $filename = implode(DIRECTORY_SEPARATOR, [
                $app['toolbox']['filesystem.storage'],
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

            $folderMetadata = $app['toolbox']['dropbox.client']
                ->getMetadataWithChildren($app['toolbox']['dropbox.root']);
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
