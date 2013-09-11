<?php
/**
 * A dependency injection container for lazy-evaluated shared resources.
 *
 * The idea is based on Pimple as Silex\Application (both by Fabien Potencier).
 * I decided that all resources I need are always shared, and that
 * simplified interface a lot.
**/
namespace Ob_Ivan\ResourceContainer;

use ArrayAccess;

class ResourceContainer implements ArrayAccess
{
    /**
     * @var [<string name> => <mixed(self) factory>]
    **/
    private $factories = [];

    /**
     * @var [<string name> => <mixed value>]
    **/
    private $values = [];

    // public : ArrayAccess //

    public function offsetExists($name)
    {
        return isset($this->values[$name]) || isset($this->factories[$name]);
    }

    public function offsetGet($name)
    {
        if (isset($this->values[$name])) {
            return $this->values[$name];
        }
        if (isset($this->factories[$name])) {
            return $this->factories[$name]($this);
        }
        throw new Exception('Trying to access unknown resource "' . $name . '"');
    }

    public function offsetSet($name, $value)
    {
        $this->values[$name] = $value;
        unset($this->factories[$name]);
    }

    public function offsetUnset($name)
    {
        unset($this->values[$name]);
        unset($this->factories[$name]);
    }

    // public : ResourceContainer //

    public function __construct(array $values = [])
    {
        $this->importValues($values);
    }

    // public : ResourceContainer : values //

    public function importValues(array $values)
    {
        foreach ($values as $name => $value) {
            $this[$name] = $value;
        }
    }

    // public : ResourceContainer : factories //

    /**
     * Register a resource factory.
     *
     * Upon access the factory is called at most once to produce a value
     * which is then stored and returned each time the value is accessed.
     * The factory will receive the container's instance as its only
     * argument.
     *
     * Example usage:
     *
     *  $container->register('alice', function ($container) {
     *      return isset($container['bob']);
     *  });
     *  $alice = $container['alice']; // false
     *  $container['bob'] = 'bob';
     *  $alice = $container['alice']; // still false as the value is already calculated.
     *
     * It is recommended that you divide your code into two stages:
     * initialization and utilization. On initialization stage you assign
     * values and register resource factories, allowing them to access
     * container from inside a function, but you never retrieve values from
     * the container. On utilization stage you assume all initialization is
     * over, and you access values and resources to perform your tasks.
     *
     *  @param  string      $name
     *  @param  mixed(self) $factory
    **/
    public function register($name, callable $factory)
    {
        $this->factories[$name] = $this->share($factory);
        unset($this->values[$name]);
    }

    /**
     * Extend a previously registered resource factory with an extender function.
     *
     * When called extender will receive two arguments:
     *  - the previously registered resource factory.
     *  - the container.
     * It is up to extender whether to call the factory or not, and when, and how.
     * Effectively extender is simply a resource factory with only difference
     * that it receives the previous factory along with container instance.
     *
     *  @param  string                      $name
     *  @param  mixed(self)(mixed(self))    $extender
    **/
    public function extend($name, callable $extender)
    {
        if (! isset($this->factories[$name])) {
            throw new Exception('Trying to extend unknown resource "' . $name . '"');
        }
        $factory = $this->factories[$name];
        $this->factories[$name] = $this->share(
            function ($container) use ($extender, $factory) {
                return $extender($factory, $container);
            }
        );
    }

    // public : ResourceContainer : providers //

    public function importProvider(ResourceProviderInterface $provider, array $values = [])
    {
        $provider->populate($this);
        $this->importValues($values);
    }

    // private //

    private function share(callable $factory)
    {
        return function ($container) use ($factory) {
            static $value;
            if (is_null($value)) {
                $value = $factory($container);
            }
            return $value;
        };
    }
}
