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

Run composer to install dependencies:

    $ composer install

Next create a front controller file (index.php) in the public folder
like following:

```php
<?php
$codeDir = <path/to/private_folder>;
require_once $codeDir . '/bootstrap.php';
$app = new Ob_Ivan\DropboxProxy\Application\WebApplication([
    'debug'                     => false, // You can set this to true in development.
    'docroot'                   => __DIR__,
    'config.path'               => $codeDir . '/config.json',
    'dropbox.auth_info.json'    => $codeDir . '/dropbox.json',
]);
$app->run();
```

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
    "code" : "<AUTHORIZATION_CODE_GOES_HERE>"
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

2. Web Application is based on the [Silex framework](https://github.com/fabpot/Silex)
which is available under
[MIT License](https://github.com/fabpot/Silex/blob/master/LICENSE).

