<?php

$uri = $_SERVER['REQUEST_URI'];

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
    exit;
}

header('HTTP/1.0 404 Not found');
print 'File ' . $uri . ' was not found on server. Check for typos or contact system administrator.';

