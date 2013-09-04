<?php

$docroot = __DIR__;
require_once $docroot . '/vendor/autoload.php';

/**
 * Read config.
 *
 * Config is a json file reposing at config.json in the document root.
 *  {
 *      debug   : Boolean which should be true only in development environment.
 *      storage : Path to the distributed files' directory relative to the document root.
 *  }
**/
$configFilename = $docroot . '/config.json';
$configContent = file_get_contents($configFilename);
$config = json_decode($configContent, true);

$app = new Silex\Application([
    'docroot' => $docroot,
    'config'  => $config,
]);
if ($config['debug']) {
    $app['debug'] = true;
}

// Routing and controllers.
$app->get('/', function () use ($app) {
    $webAuthBuilder = new Ob_Ivan\DropboxProxy\WebAuthBuilder(
        $app['docroot'] . '/dropbox.json',
        'DownloadProxy/0.1'
    );
    // TODO: folder listing
    return 'Folder listing is not yet supported. Please come back later.';
});

$app->get('/{file}', function ($file) use ($app) {
    // Download a file.
    $filename = implode(DIRECTORY_SEPARATOR, [$app['docroot'], $app['config']['storage'], $file]);
    if (! file_exists($filename)) {
        return
            'File "' . $file . '" was not found on server. ' .
            'Check for typos or contact system administrator.'
        ;
    }
    return $app->sendFile($filename);
});

$app->run();
