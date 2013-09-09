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
use Symfony\Component\Console\Application as ConsoleApplication;

class CliApplication extends ConsoleApplication
{
    /**
     * @var Application
    **/
    private $app;

    public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);

        $this->app = new Application($values);
    }

    public function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();

        $uploadCommand = new UploadCommand();
        $uploadCommand->setResourceContainer($this->app);
        $defaultCommands[] = $uploadCommand;

        return $defaultCommands;
    }
}
