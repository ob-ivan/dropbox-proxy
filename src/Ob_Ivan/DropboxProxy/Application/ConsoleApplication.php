<?php
/**
 * An application handling command line invocations.
 *
 * Available commands:
 *  upload [FILE]
 *      If FILE is a relative path to a file within local storage,
 *      upload it into dropbox folder under the same name.
 *      If FILE is omitted, uploads each file within local storage.
 *      Directories are ignored and not walked recursively.
**/
namespace Ob_Ivan\DropboxProxy\Application;

use Ob_Ivan\DropboxProxy\Command\UploadCommand;
use Ob_Ivan\DropboxProxy\ResourceContainerFactory;
use Symfony\Component\Console\Application as WrappedApplication;

class ConsoleApplication
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
        $container = ResourceContainerFactory::getResourceContainer($configPath, $storagePath);

        // TODO: Inject $container into UploadCommand.
        $this->app = new WrappedApplication();
        $this->app->add(new UploadCommand());
    }

    public function run()
    {
        $this->app->run();
    }
}
