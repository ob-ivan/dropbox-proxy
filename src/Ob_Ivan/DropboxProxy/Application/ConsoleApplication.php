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
use Ob_Ivan\DropboxProxy\ResourceProvider\DropboxResourceProvider;
use Ob_Ivan\ResourceContainer\ResourceContainer;
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
        $container = new ResourceContainer();
        $container->importProvider(new DropboxResourceProvider());
        $container->importValues($config);

        // TODO: Inject container into UploadCommand.
        $this->app = new WrappedApplication();
        $this->app->add(new UploadCommand());
    }

    public function run()
    {
        $this->app->run();
    }
}
