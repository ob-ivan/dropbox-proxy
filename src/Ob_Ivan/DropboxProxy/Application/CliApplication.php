<?php
/**
 * An application handling command line invocations.
 *
 * Available commands:
 *  upload FILE
 *  upload DIRECTORY
 *  upload
**/
namespace Ob_Ivan\DropboxProxy\Application;

class CliApplication
{
    /**
     * @var Application
    **/
    private $app;

    public function __construct(array $values = [])
    {
        $this->app = new Application($values);
    }

    public function handle($argv)
    {
    }
}
