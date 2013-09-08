<?php
/**
 * An empty application with a minimal set of pre-registered providers.
**/
namespace Ob_Ivan\DropboxProxy\Application;

use Ob_Ivan\DropboxProxy\Provider\ConfigServiceProvider;
use Ob_Ivan\DropboxProxy\Provider\DropboxServiceProvider;
use Silex\Application as ParentApplication;
use Silex\Application\UrlGeneratorTrait;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;

class Application extends ParentApplication
{
    use UrlGeneratorTrait;

    public function __construct(array $values = [])
    {
        parent::__construct($values);

        $this->register(new ConfigServiceProvider());
        $this->register(new DropboxServiceProvider());
        $this->register(new SessionServiceProvider());
        $this->register(new UrlGeneratorServiceProvider());

        // Set a bridge between [dropbox] and [config] services.

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

        // Assign default values for parameters.

        $this['dropbox.client_identifier'] = 'DownloadProxy/0.1';
    }
}
