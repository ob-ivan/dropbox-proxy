<?php

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
});

$docroot = __DIR__;
require_once $docroot . '/vendor/autoload.php';

$app = new Ob_Ivan\DropboxProxy\Application([
    'docroot' => $docroot,
]);
if ($app['config']['debug']) {
    $app['debug'] = true;
}

// Additional services //

// TODO: Encapsulate these in a DropboxServiceProvider and publish it.
$app['dropbox.access_token'] = $app->share(function () use ($app) {
    if (! isset($app['config']['accessToken'])) {
        /**
         * TODO: Output the instruction.
         *  - Go to /dropbox-auth-start
         *  - Get access code and store it to config.json.
         *  - Go to /dropbox-auth-finish
         *  - Get accessToken and store it to config.json.
         *  - Done.
        **/
        /**
         * TODO: Automate the process.
        **/
        throw new Exception('No accessToken is set in config.');
    }
    return $app['config']['accessToken'];
});
$app['dropbox.app_info'] = $app->share(function () use ($app) {
    return Dropbox\AppInfo::loadFromJsonFile($app['docroot'] . '/dropbox.json');
});
$app['dropbox.client'] = $app->share(function () use ($app) {
    return new Dropbox\Client($app['dropbox.access_token'], $app['dropbox.client_identifier']);
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
    // Redirect to Dropbox page and generate an authorization code.
    return $app->redirect($app['dropbox.web_auth']->start());
})->bind('dropbox-auth-start');

$app->get('/dropbox-auth-finish', function () use ($app) {
    try {
        // Authorization code can be used only once to obtain accessToken.
        list($accessToken, $userId) = $app['dropbox.web_auth']->finish($app['config']['code']);
    }
    catch (Dropbox\Exception $e) {
        throw new Exception('Error communicating with Dropbox API: ' . $e->getMessage(), 0, $e);
    }
    return $accessToken;
})->bind('dropbox-auth-finish');

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

    $folderMetadata = $app['dropbox.client']->getMetadataWithChildren('/');
    return '<pre>' . print_r($folderMetadata, true) . '</pre>'; // debug

    // TODO: folder listing
    return 'Folder listing is not yet supported. Please come back later.';
});

// Handle request.
$app->run();
