<?php
/**
 * An application handling command line invocations.
 *
 * Available commands:
 *  upload [FILES...]
 *      For each FILE if it is a relative path to a file within local storage,
 *      upload it into dropbox folder under the same name.
 *      If FILE is omitted, uploads each file within local storage.
 *      Directories are ignored and not walked recursively.
**/
namespace Ob_Ivan\DropboxProxy\Application;

use Ob_Ivan\DropboxProxy\Command\UploadCommand;
use Ob_Ivan\DropboxProxy\ToolboxFactory;
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
        $toolbox = ToolboxFactory::getToolbox($configPath, $storagePath);

        $this->app = new WrappedApplication();
        $command = new UploadCommand();
        $command->setToolbox($toolbox);
        $this->app->add($command);
    }

    public function run()
    {
        $this->app->run();
    }
}
