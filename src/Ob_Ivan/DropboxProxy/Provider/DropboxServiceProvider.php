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
 *      header while making API requests. Refer to Dropbox API docs for details.
 *
 * Services:
 *  - [dropbox.access_token]
 *  - [dropbox.app_info]
 *  - [dropbox.client]
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
