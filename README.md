DropboxProxy
============

A web interface for browsing and downloading files from a Dropbox folder
accompanied by a shell utility for uploading from and download to
a local folder.

Installation
============

This package provides functionality for both a web proxy and a shell utility.
You may choose to install any of them, or both. In any case some initial setup
is required.

Inital setup
------------
Get the code (substitute `REPOSITORY` and `CODE_DIR` as apropriate):

    $ git clone REPOSITORY CODE_DIR

And run [composer](http://getcomposer.org/) to install dependencies:

    $ cd CODE_DIR
    $ composer install

Create a configuration file (usually `config.json` but you are free to name it
any way you like) using `app/config.example.json` for reference.
There is `app/` directory to put your application files such as configuration
there. You can choose not to use it and create configuration file anywhere you
prefer. Just remember that config holds confidential data, and thus it should
not be made public.

Installing web proxy
--------------------
In your web folder create two files, `index.php` and `.htaccess`.

```php
<?php
// index.php
require_once 'CODE_DIR/bootstrap.php';
$app = new Ob_Ivan\DropboxProxy\Application\WebApplication(
    'APP_DIR/config.json', // Put the path to your config file here.
    [
        'filesystem.storage' => STORAGE_DIR,
    ]
);,
$app->run();
```

```
# .htaccess
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule (.*) index.php [L]
```

**TODO:** Write a location for `nginx.conf`.

Installing console utility
--------------------------
In your app folder (or actually anywhere you prefer) create an executable
script file:

```php
#!/usr/local/bin/php
<?php
// console.php
require_once 'CODE_DIR/bootstrap.php';
$app = new Ob_Ivan\DropboxProxy\Application\ConsoleApplication(
    'APP_DIR/config.json', // Put the path to your config file here.
    [
        'filesystem.storage' => STORAGE_DIR,
    ]
);,
$app->run();
```

The resemblance of this file to `index.php` is intentional.

In the first line (the so-called shebang line) put the path to your local
php interpreter. You can find it using `which` utility:

    $ which php
    /usr/local/bin/php

Obtaining access token
----------------------
Any actual requests to Dropbox API are out of question unless you get an access
token and put it to your config file.

_(Instructions below assume you installed web proxy.)_

To do that make sure you are logged into Dropbox in your browser and
go to `http://<your.domain>/dropbox-auth-start`. It will show you
a Dropbox page asking whether you are willing to grant your app an
access to your folder. Click accept and you will see an **authorization
code** in your browser. It looks like a lengthy string of alphanumeric
characters. Add it to `config.json` like follows:

```json
{
    "dropbox.auth_code" : "<AUTHORIZATION_CODE_GOES_HERE>"
}
```

Then when you visit `http://<your.domain>/dropbox-auth-finish` it will
show you the **access token**, which looks pretty much like the authorization
code but is of different nature. Place it into `config.json` instead of
authorization code:

```json
{
    "dropbox.access_token" : "<ACCESS_TOKEN_GOES_HERE>"
}
```

We remove authorization code because it can be used only once to obtain
the access token. This also means that if you don't store the access token
you'll have to start over from `http://<your.domain>/dropbox-auth-start`.

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

