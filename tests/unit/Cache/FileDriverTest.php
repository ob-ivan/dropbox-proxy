<?php

use Ob_Ivan\Cache\Driver\FileDriver;

class FileDriverTest extends PHPUnit_Framework_TestCase
{
    public function testInstantiate()
    {
        new FileDriver([
            'file_prefix' => '/tmp',
        ]);
    }
}
