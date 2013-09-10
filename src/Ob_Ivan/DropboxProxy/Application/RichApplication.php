<?php
/**
 * A Silex-style application enriched with several utility traits.
**/
namespace Ob_Ivan\DropboxProxy\Application;

use Silex\Application as ParentApplication;
use Silex\Application\UrlGeneratorTrait;

class RichApplication extends ParentApplication
{
    use UrlGeneratorTrait;
}
