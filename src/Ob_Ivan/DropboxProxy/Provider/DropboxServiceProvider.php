<?php
/**
 * Provider for a set of services under [dropbox.] namespace
 * in a Silex application.
 *
 * Parameters:
 *  - [dropbox.auth_code]
 *      An authorization code received from Dropbox website when you call
 *      [dropbox.web_auth]->start(). It can be used only once to obtain
 *      and store [dropbox.access_token] value.
 *
 *  - [dropbox.app_info.json]
 *      Absolute path to a json file with your app credentials (key and secret).
 *      Used by [dropbox.app_info] service only. May be omitted if you already
 *      have [dropbox.access_token] value stored.
 *
 *  - [dropbox.client_identifier]
 *      Your application name and a version which will be used to form User-Agent
 *      header while making API requests with [dropbox.client] service.
 *      Refer to Dropbox API docs for details on how to choose a proper value.
 *
 * Services:
 *  - [dropbox.access_token]
 *      A string value used to prove your app's identity while making requests.
 *      This is a service because its default behavior is to lookup for
 *      [dropbox.auth_code] parameter and call [dropbox.web_auth] service
 *      to obtain the access token value. Note that in app case it is your
 *      responsibility to store the access token value, or else the auth code
 *      value will be wasted.
 *      Once you have stored access token value you can substitute app service
 *      with its value in your application:
 *          $app['dropbox.access_token'] = file_get_contents('access_token.txt');
 *
 *  - [dropbox.app_info]
 *      An instance of Dropbox\AppInfo class which look ups [dropbox.app_info.json]
 *      file and reads it contents. It is used by [dropbox.web_auth] only, so
 *      you can ignore it if you have already stored the access token value.
 *
 *  - [dropbox.client]
 *      An instance of Dropbox\Client class which you call to make actual API
 *      requests. It requires [dropbox.client_identifier] to be defined and
 *      [dropbox.access_token] to be valid (obtained during OAuth2 authoriztion
 *      process and unexpired), otherwise exceptions will be thrown.
 *
 *  - [dropbox.web_auth]
 *      An instance of Dropbox\WebAuth used in the authorization process to
 *      initialize it by calling its start() method, and to obtain access token
 *      by calling finish() method with auth code as its argument.
 *
 * Usage:
 *  To perform actual requests to Dropbox API you need an access token.
 *  If you have one provide its value to [dropbox.access_token] explicitly,
 *  and you are set to start using [dropbox.client] object.
 *
 *  If you don't have an access token, you'll have to go through the authorization
 *  process which starts with storing your app's credentials in a json file
 *  and providing [dropbox.app_info.json] with a path to that file.
 *  Once app key and secret are known you can call [dropbox.web_auth]->start()
 *  and it will return you a website link. Log in to Dropbox with your browser
 *  and open that link.
 *
 *  There Dropbox will ask you if you are willing to reveal all the secrets
 *  your Dropbox folder conceals to your app, and of course you are because
 *  you are still in full control of what your app makes downloadable by the
 *  public and what not. Click 'Accept' and you will receive the authorization
 *  code. Provide its value to [dropbox.auth_code] and next time you access
 *  [dropbox.access_token] it will contain the longly desired access token
 *  value. It is recommended that you store it and from now on provide its
 *  value to [dropbox.access_token] explicitly.
 *
 *  Please refer to Dropbox\Client full documentation on what overwhelming
 *  possibilities it brings into your hands.
**/
namespace Ob_Ivan\DropboxProxy\Provider;

use Dropbox;
use Silex\Application;
use Silex\ServiceProviderInterface;

class DropboxServiceProvider implements ServiceProviderInterface
{
    function register(Application $app)
    {
        $app['dropbox.access_token'] = $app->share(function () use ($app) {
            try {
                // Authorization code can be used only once to obtain accessToken.
                list($accessToken, $userId) =
                    $app['dropbox.web_auth']->finish($app['dropbox.auth_code']);
            }
            catch (Dropbox\Exception $e) {
                throw new Exception(
                    'Error communicating with Dropbox API: ' . $e->getMessage(),
                    0,
                    $e
                );
            }
            return $accessToken;
        });
        $app['dropbox.app_info'] = $app->share(function () use ($app) {
            return Dropbox\AppInfo::loadFromJsonFile($app['dropbox.app_info.json']);
        });
        $app['dropbox.client'] = $app->share(function () use ($app) {
            return new Dropbox\Client(
                $app['dropbox.access_token'],
                $app['dropbox.client_identifier']
            );
        });
        $app['dropbox.web_auth'] = $app->share(function () use ($app) {
            return new Dropbox\WebAuthNoRedirect(
                $app['dropbox.app_info'],
                $app['dropbox.client_identifier']
            );
        });
    }

    function boot(Application $app)
    {
    }
}
