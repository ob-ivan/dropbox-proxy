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
    }
}
