DropboxProxy
============

PHP proxy allowing downloading files from a single Dropbox account
as if they were kept on the server.

Usage
=====
Consider the following folder structure:

    ~/public_html       The one web server has access to.
    ~/private_folder    Not visible from the web.

First of all you'll have to clone the repo to the private folder:

    $ git clone git@github.com:ob-ivan/dropbox-proxy.git ~/private_folder

Next create a front controller file (index.php) in the public folder
like following:

    <?php
    $codeDir = <path/to/private_folder>;
    require_once $codeDir . '/bootstrap.php';
    $app = new Ob_Ivan\DropboxProxy\Application\WebApplication([
        'config.path'   => $codeDir . '/config.json',
        'docroot'       => __DIR__,
    ]);
    $app->run();

You'll need the `config.json` file mentioned above. It may contain
an empty object at first, and that's enough for your proxy to run.
But as it knows nothing about your account or your dropbox folder,
you'll have to generate an access token and put it into `config.json`.

To do that make sure you are logged into Dropbox in your browser and
go to http://<your.domain>/dropbox-auth-start. It will show you
a Dropbox page asking whether you are willing to grant your app an
access to your folder. Click accept and you will see an **authorization
code** in your browser. It looks like a lengthy string of alphanumeric
characters. Put it into `config.json` like follows:

    {
        "code" : "<AUTHORIZATION_CODE_GOES_HERE>"
    }

Then when you visit http://<your.domain>/dropbox-auth-finish it will
show you the **access token**, which looks pretty much like the authorization
token but is of different nature. Place it into `config.json` instead of
authorization code:

    {
        "accessToken" : "<ACCESS_TOKEN_GOES_HERE>"
    }

We remove authorization code because it can be used only once to obtain
the access token. This also means that if you don't store the access token
you'll have to go again to http://<your.domain>/dropbox-auth-start.

