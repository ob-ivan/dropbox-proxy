<?php
/**
 * A Silex-style application enriched with several utility providers and traits.
**/
namespace Ob_Ivan\DropboxProxy\Application;

use Silex\Application as ParentApplication;
use Silex\Application\UrlGeneratorTrait;

class RichApplication extends ParentApplication
{
    use UrlGeneratorTrait;

    public function __construct($values = [])
    {
        parent::__construct($values);

        foreach ($this->getDefaultServiceProviders() as $serviceProvider) {
            $app->register($serviceProvider);
        }
    }

    // protected //

    /**
     * Return the list of service providers to be registered upon instantiation.
     *
     *  @return [Silex\ServiceProviderInterface]
    **/
    protected function getDefaultServiceProviders()
    {
        return [
            new SessionServiceProvider(),
            new UrlGeneratorServiceProvider(),
        ];
    }
}
