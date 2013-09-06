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
 *      to obtain the access token value. Note that in this case it is your
 *      responsibility to store the access token value, or else the auth code
 *      value will be wasted.
 *      Once you have stored access token value you can substitute this service
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
 *
 * Workflow
**/
namespace Ob_Ivan\DropboxProxy\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class DropboxServiceProvider implements ServiceProviderInterface
{
    function register(Application $app)
    {
    }

    function boot(Application $app)
    {
    }
}
