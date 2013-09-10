<?php
/**
 * An application handling command line invocations.
 *
 * Available commands:
 *  upload FILE
 *  upload DIRECTORY
 *  upload              # reads config
**/
namespace Ob_Ivan\DropboxProxy\Application;

use Ob_Ivan\DropboxProxy\Command\UploadCommand;
use Symfony\Component\Console\Application as WrappedApplication;

class ConsoleApplication
{
    /**
     * @var WrappedApplication
    **/
    private $app;

    public function __construct($configPath)
    {
        // Locate config at $configPath and read it.
        // TODO: Eliminate code mirroring with WebApplication::__construct.
        $config = json_decode(file_get_contents($configPath), true);

        // TODO: Inject config values into application.
        // TODO: Inject DropboxServiceProvider into application.
        $this->app = new WrappedApplication();
        $this->app->add(new UploadCommand());
    }

    public function run()
    {
        $this->app->run();
    }
}
