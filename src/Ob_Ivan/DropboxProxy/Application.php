<?php
namespace Ob_Ivan\DropboxProxy;

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

        $this->register(new SessionServiceProvider());
        $this->register(new UrlGeneratorServiceProvider());
    }
}
