<?php
namespace Ob_Ivan\DropboxProxy\ServiceProvider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\Provider\TwigServiceProvider as WrappedProvider;
use Twig_SimpleFunction;

class TwigServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app->register(new WrappedProvider());
    }

    public function boot(Application $app)
    {
        $app['twig'] = $app->share($app->extend('twig', function ($twig, $app) {

            // функции //

            $twig->addFunction(new Twig_SimpleFunction('basename',  function ($filepath) { return basename($filepath); }));

            return $twig;
        }));
    }
}
