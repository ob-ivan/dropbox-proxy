<?php

/**
 * Read config.
 *
 * Config is a json file reposing at config.json in the document root.
 *  {
 *      baseUrl : String each request's path starts with. Defaults to '/'.
 *      storage : Path to the distributed files' directory relative to the document root.
 *  }
**/
$docroot = __DIR__;
$configFilename = $docroot . '/config.json';
$configContent = file_get_contents($configFilename);
$config = json_decode($configContent, true);

// Determine command.
$uri = $_SERVER['REQUEST_URI'];
$action = $uri === '/score/' ? 'list_folder' : 'download_file';

// Execute controller.
switch ($action) {
    case 'list_folder':
        print 'Folder listing is not yet supported. Please come back later.';
        break;

    case 'download_file':
        $filename = dirname(__FILE__) . $uri;

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
            print 'File ' . $uri . ' was not found on server. Check for typos or contact system administrator.';
        }
        break;

    default:
        print 'Unknown command "' . $action . '".';
        break;
}

