<?php
/**
 * A minimal set of instructions to enable DropboxProxy project
 * in your scripts. Include this file, and start using classes
 * from the Ob_Ivan\DropboxProxy namespace right away.
 *
 * You will have to run `composer install` in this folder in order
 * to obtain dependencies.
 *
 * See README for further usage notes.
**/

// Set an aggressive error handler which does not allow you to ignore
// warnings, notices and strict standards.
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
});

// Register autoloader for dependencies as well as for core source code.
require_once __DIR__ . '/vendor/autoload.php';
