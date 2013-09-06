<?php
namespace Ob_Ivan\DropboxProxy\Application;

class WebApplication extends Application
{
    public function __construct(array $values = [])
    {
        parent::__construct($values);

        // Additional services //

        $this['dropbox.client_identifier'] = 'DownloadProxy/0.1';
        $accessTokenFactory = $this->raw('dropbox.access_token');
        $this['dropbox.access_token'] = $this->share(function () use ($accessTokenFactory) {
            if (isset($this['config']['accessToken'])) {
                return $this['config']['accessToken'];
            }
            return $accessTokenFactory($this);
        });
        $this['dropbox.auth_code'] = $this->share(function () {
            if (isset($this['config']['authCode'])) {
                return $this['config']['authCode'];
            }
        });

        // Routing and controllers //

        $this->get('/dropbox-auth-start', function () {
            // Redirect to Dropbox page and generate an authorization code.
            return $this->redirect($this['dropbox.web_auth']->start());
        })->bind('dropbox-auth-start');

        $this->get('/dropbox-auth-finish', function () {
            return $this['dropbox.access_token'];
        })->bind('dropbox-auth-finish');

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

        $this->get('/', function () {

            $folderMetadata = $this['dropbox.client']->getMetadataWithChildren('/');
            return '<pre>' . print_r($folderMetadata, true) . '</pre>'; // debug

            // TODO: folder listing
            return 'Folder listing is not yet supported. Please come back later.';
        });
    }
}
