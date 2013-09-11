<?php
/**
 * A dependency injection container for lazy-evaluated shared resources.
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
        throw new Exception('Unknown resource "' . $name . '"');
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
        foreach ($values as $name => $value) {
            $this[$name] = $value;
        }
    }

    public function register($name, callable $factory)
    {
        $this->factories[$name] = $this->share($factory);
        unset($this->values[$name]);
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
