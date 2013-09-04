<?php

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
});

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

$app = new Ob_Ivan\DropboxProxy\Application([
    'docroot' => $docroot,
    'config'  => $config,
]);
if ($config['debug']) {
    $app['debug'] = true;
}

// Additional services //

$app['dropbox.app_info'] = $app->share(function () use ($app) {
    return Dropbox\AppInfo::loadFromJsonFile($app['docroot'] . '/dropbox.json');
});
$app['dropbox.client'] = $app->share(function () use ($app) {
    try {
        // TODO: Понять, откуда брать код авторизации, потому что пока что
        // эксперимент показывает, что он одноразовый.
        list($accessToken, $userId) = $app['dropbox.web_auth']->finish($app['config']['code']);
    }
    catch (Dropbox\Exception $e) {
        throw new Exception('Error communicating with Dropbox API: ' . $e->getMessage(), 0, $e);
    }
    return new Dropbox\Client($accessToken, $app['dropbox.client_identifier']);
});
$app['dropbox.client_identifier'] = 'DownloadProxy/0.1';
$app['dropbox.web_auth'] = $app->share(function () use ($app) {
    return new Dropbox\WebAuthNoRedirect(
        $app['dropbox.app_info'],
        $app['dropbox.client_identifier']
    );
});

// Routing and controllers //

$app->get('/dropbox-auth-start', function () use ($app) {
    // Redirect to Dropbox page and generate an authorization token.
    return $app->redirect($app['dropbox.web_auth']->start());
})->bind('dropbox-auth-start');

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

$app->get('/', function () use ($app) {

    print_r($app['dropbox.client']->getAccountInfo()); die; // debug

    // TODO: folder listing
    return 'Folder listing is not yet supported. Please come back later.';
});

// Handle request.
$app->run();
