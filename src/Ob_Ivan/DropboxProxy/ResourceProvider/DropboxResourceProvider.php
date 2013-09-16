<?php
/**
 * Provider for a set of resources under [dropbox.] namespace
 * in a resource container.
 *
 * Parameters:
 *  - [dropbox.auth_code]
 *      An authorization code received from Dropbox website when you call
 *      [dropbox.web_auth]->start(). It can be used only once to obtain
 *      and store [dropbox.access_token] value.
 *
 *  - [dropbox.app_info.json]
 *      A PHP array with your app's credentials (key and secret).
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
 *      Once you have stored access token value you can substitute container resource
 *      with its value in your application:
 *          $container['dropbox.access_token'] = file_get_contents('access_token.txt');
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
namespace Ob_Ivan\DropboxProxy\ResourceProvider;

use Dropbox;
use Ob_Ivan\ResourceContainer\ResourceContainer;
use Ob_Ivan\ResourceContainer\ResourceProviderInterface;

class DropboxResourceProvider implements ResourceProviderInterface
{
    function populate(ResourceContainer $container)
    {
        // level 0 //

        /**
         * Expected parameters:
         *  - [dropbox.app_info.json]
         *  - [dropbox.auth_code]
         *  - [dropbox.client_identifier]
        **/

        // level 1 //

        $container->register('dropbox.app_info', function ($container) {
            return Dropbox\AppInfo::loadFromJson($container['dropbox.app_info.json']);
        });

        // level 2 //

        $container->register('dropbox.web_auth', function ($container) {
            return new Dropbox\WebAuthNoRedirect(
                $container['dropbox.app_info'],
                $container['dropbox.client_identifier']
            );
        });

        // level 3 //

        $container->register('dropbox.access_token', function ($container) {
            try {
                // Authorization code can be used only once to obtain accessToken.
                list($accessToken, $userId) =
                    $container['dropbox.web_auth']->finish($container['dropbox.auth_code']);
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

        // level 4 //

        $container->register('dropbox.client', function ($container) {
            return new Dropbox\Client(
                $container['dropbox.access_token'],
                $container['dropbox.client_identifier']
            );
        });
    }
}
