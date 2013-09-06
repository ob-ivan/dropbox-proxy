<?php
namespace Ob_Ivan\DropboxProxy\Application;

class WebApplication extends Application
{
    public function __construct(array $values = [])
    {
        parent::__construct($values);

        // Additional services //

        // TODO: Encapsulate these in a DropboxServiceProvider and publish it.
        /**
         * TODO: Output the instruction.
         *  - Go to /dropbox-auth-start
         *  - Get access code and store it to config.json.
         *  - Go to /dropbox-auth-finish
         *  - Get accessToken and store it to config.json.
         *  - Done.
        **/
        /**
         * TODO: Automate the process.
        **/
        /*
        // TODO: Find a decent way to link [dropbox.access_token] to [config][access_token].
        $this['dropbox.access_token'] = $this->share(function () {
            if (! isset($this['config']['accessToken'])) {
                throw new Exception('No accessToken is set in config.');
            }
            return $this['config']['accessToken'];
        });
        */
        $this['dropbox.app_info'] = $this->share(function () {
            return Dropbox\AppInfo::loadFromJsonFile($this['docroot'] . '/dropbox.json');
        });
        $this['dropbox.client'] = $this->share(function () {
            return new Dropbox\Client($this['dropbox.access_token'], $this['dropbox.client_identifier']);
        });
        // $this['dropbox.client_identifier'] = 'DownloadProxy/0.1';
        $this['dropbox.web_auth'] = $this->share(function () {
            return new Dropbox\WebAuthNoRedirect(
                $this['dropbox.app_info'],
                $this['dropbox.client_identifier']
            );
        });

        // Routing and controllers //

        $this->get('/dropbox-auth-start', function () {
            // Redirect to Dropbox page and generate an authorization code.
            return $this->redirect($this['dropbox.web_auth']->start());
        })->bind('dropbox-auth-start');

        $this->get('/dropbox-auth-finish', function () {
            try {
                // Authorization code can be used only once to obtain accessToken.
                list($accessToken, $userId) = $this['dropbox.web_auth']->finish($this['config']['code']);
            }
            catch (Dropbox\Exception $e) {
                throw new Exception('Error communicating with Dropbox API: ' . $e->getMessage(), 0, $e);
            }
            return $accessToken;
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
