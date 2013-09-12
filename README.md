DropboxProxy
============

A web interface for browsing and downloading files from a Dropbox folder
accompanied by a shell utility for uploading from and download to
a local folder.

Usage
=====
There are four directories to be taken into consideration:

    WEB         The one web server has access to.
    STORAGE     A local directory to upload files from and download to.
    CODE        A directory where this repo abides.
    APP         Configuration folder.

It is strongly recommended that `CODE` and `APP` directories were kept off the
web server access.

First of all get the code (substitute `CODE` metavariable with appropriate path):

    $ git clone git@github.com:ob-ivan/dropbox-proxy.git CODE

And run [composer](http://getcomposer.org/) to install dependencies:

    $ cd CODE
    $ composer install

Next you'll have to create a config file and put it into `APP` directory.
You can use `CODE/app` subdirectory for this, or create another one on your own.

    $ echo '{}' > APP/config.json

The config file is empty now, we'll elaborate on its contents later.

Next create `WEB/index.php` like following (substitute directory placeholders
with appropriate paths):

```php
<?php
// WEB/index.php
require_once 'CODE/bootstrap.php';
$app = new Ob_Ivan\DropboxProxy\Application\WebApplication('APP/config.json', [
    'filesystem.storage' => STORAGE,
]);,
$app->run();
```

You'll also need a rewrite module so that requests like `/The_Batman.ISO` could
be handled by your `index.php`. If you are running Apache web server, create
`.htaccess` file like following:

```
# WEB/.htaccess
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule (.*) index.php [L]
```

**TODO:** Write instructions for `nginx.conf` location.

The web interface is ready to run, but it will show errors as your config file
is empty.

### TODO: Rewrite everything below.

You'll need to create two files mentioned in the above code block.
`dropbox.json` must contain the app key and secret which you
can receive on the Dropbox site when you register an app.
Go to https://www.dropbox.com/developers/apps if you don't have one.

You will also need the `config.json` file. It may contain an empty
object at first, and that's enough for your proxy to run.
But as it knows nothing about your account or your dropbox folder,
you'll have to generate an access token and put it into `config.json`.

To do that make sure you are logged into Dropbox in your browser and
go to `http://<your.domain>/dropbox-auth-start`. It will show you
a Dropbox page asking whether you are willing to grant your app an
access to your folder. Click accept and you will see an **authorization
code** in your browser. It looks like a lengthy string of alphanumeric
characters. Put it into `config.json` like follows:

```json
{
    "authCode" : "<AUTHORIZATION_CODE_GOES_HERE>"
}
```

Then when you visit `http://<your.domain>/dropbox-auth-finish` it will
show you the **access token**, which looks pretty much like the authorization
token but is of different nature. Place it into `config.json` instead of
authorization code:

```json
{
    "accessToken" : "<ACCESS_TOKEN_GOES_HERE>"
}
```

We remove authorization code because it can be used only once to obtain
the access token. This also means that if you don't store the access token
you'll have to start over from `http://<your.domain>/dropbox-auth-start`.

Now you can see the reason why we put `config.json` and `dropbox.json`
to the private folder. This is because both of them contain sensitive data
which should be kept safe even if web server fails to deny access to
distinct files in the public folder.

MIT License
===========
You can use this code on terms defined per MIT License. See LICENSE.txt for details.

Used Software
=============
1. This whole project would be impossible without
[Dropbox PHP SDK](https://www.dropbox.com/developers/core/sdks/php) which is
generously provided by Dropbox Inc. under
[MIT License](https://github.com/dropbox/dropbox-sdk-php/blob/master/License.txt).

2. Web Application is based on the [Silex framework](http://silex.sensiolabs.org/)
which is available under [MIT License](https://github.com/fabpot/Silex/blob/master/LICENSE).

