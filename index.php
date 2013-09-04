<?php

$docroot = __DIR__;
require_once $docroot . '/vendor/autoload.php';

/**
 * Read config.
 *
 * Config is a json file reposing at config.json in the document root.
 *  {
 *      baseUrl : String each request's path starts with. Defaults to '/'.
 *      storage : Path to the distributed files' directory relative to the document root.
 *  }
**/
$configFilename = $docroot . '/config.json';
$configContent = file_get_contents($configFilename);
$config = json_decode($configContent, true);

// Determine command.
$uri = $_SERVER['REQUEST_URI'];
if (substr($uri, 0, strlen($config['baseUrl'])) !== $config['baseUrl']) {
    print 'Unexpected request path. Expecting request with base url "' . $config['baseUrl'] . '"';
    die;
}
$uri = substr($uri, strlen($config['baseUrl']));
$action = empty($uri) ? 'list_folder' : 'download_file';

// Execute controller.
switch ($action) {
    case 'list_folder':
        $appInfo = Dropbox\AppInfo::loadFromJsonFile($docroot . '/dropbox.json');
        $webAuth = new Dropbox\WebAuth($appInfo, 'DownloadProxy/0.1');
        // TODO
        print 'Folder listing is not yet supported. Please come back later.';
        break;

    case 'download_file':
        $filename = implode(DIRECTORY_SEPARATOR, [$docroot, $config['storage'], $uri]);

        if (file_exists($filename)) {
            // copy-pasted from php.net/readfile
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($filename));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filename));
            ob_clean();
            flush();
            readfile($filename);
        } else {
            header('HTTP/1.0 404 Not found');
            print 'File "' . $uri . '" was not found on server. Check for typos or contact system administrator.';
        }
        break;

    default:
        print 'Unknown command "' . $action . '".';
        break;
}

