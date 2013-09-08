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

class WebApplication extends Application
{
    public function __construct(array $values = [])
    {
        parent::__construct($values);

        // Routing and controllers //

        // Obtain access token with a two-step authorization.
        $this->get('/dropbox-auth-start', function () {
            // Redirect to Dropbox page and generate an authorization code.
            return $this->redirect($this['dropbox.web_auth']->start());
        })->bind('dropbox-auth-start');

        $this->get('/dropbox-auth-finish', function () {
            return $this['dropbox.access_token'];
        })->bind('dropbox-auth-finish');

        // Download a file.
        $this->get('/{file}', function ($file) {
            // Download a file.
            $filename = implode(DIRECTORY_SEPARATOR, [$this['docroot'], $this['config']['storage'], $file]);
            if (! file_exists($filename)) {
                return
                    'File "' . $file . '" was not found on server. ' .
                    'Check for typos or contact system administrator.'
                ;
            }
            return $this->sendFile($filename);
        });

        // List available files.
        $this->get('/', function () {

            $folderMetadata = $this['dropbox.client']->getMetadataWithChildren($this['config']['root']);
            $contents = $folderMetadata['contents'];
            return '<pre>' . print_r($contents, true) . '</pre>'; // debug

            // TODO: folder listing
            return 'Folder listing is not yet supported. Please come back later.';
        });
    }
}
