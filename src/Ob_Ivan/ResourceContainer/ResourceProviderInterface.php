<?php
/**
 * An interface for a class that once passed to container's importProvider
 * method will enroll resources it provides in the container with poplate
 * method.
 *
 * The idea is based on Silex\ServiceProviderInterface by Fabien Potencier.
**/
namespace Ob_Ivan\ResourceContainer;

interface ResourceProviderInterface
{
    /**
     * This method is called by container upon call to its
     * importProvider method.
     *
     *  @param  ResourceContainer   $container
    **/
    public function populate(ResourceContainer $container);
}
