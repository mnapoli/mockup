<?php

namespace Mockup;

use Mockup\Spy\ObjectInspector;
use ProxyManager\Factory\AccessInterceptorValueHolderFactory;
use ProxyManager\Factory\NullObjectFactory;
use ReflectionClass;
use ReflectionMethod;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Mockup
{
    /**
     * @var ObjectInspector[]
     */
    private static $mockInspectors = [];

    /**
     * Create a mock from a class or interface name.
     */
    public static function mock($classname, array $methodOverrides = [])
    {
        if (!is_string($classname)) {
            throw new \InvalidArgumentException('Expected class name');
        }

        $reflection = new \ReflectionClass($classname);

        $object = (new NullObjectFactory)->createProxy($classname);

        return self::spyInvokations($object, $methodOverrides, $reflection);
    }

    /**
     * Spy invokations of an object.
     */
    public static function spy($object, array $methodOverrides = [])
    {
        if (!is_object($object)) {
            throw new \InvalidArgumentException('Expected object');
        }

        $reflection = new \ReflectionClass($object);

        return self::spyInvokations($object, $methodOverrides, $reflection);
    }

    /**
     * @return ObjectInspector
     */
    public static function inspect($mock)
    {
        $id = spl_object_hash($mock);
        return self::$mockInspectors[$id];
    }

    private static function spyInvokations($object, array $methodOverrides, ReflectionClass $reflection)
    {
        $factory = new AccessInterceptorValueHolderFactory;
        $mock = $factory->createProxy($object);
        $id = spl_object_hash($mock);
        self::$mockInspectors[$id] = new ObjectInspector();

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isStatic()) {
                continue;
            }

            $prefix = function ($proxy, $instance, $method, $parameters, &$returnEarly) use ($id, $methodOverrides) {
                if (array_key_exists($method, $methodOverrides)) {
                    $returnEarly = true;
                    $returnValue = $methodOverrides[$method];
                    if ($returnValue instanceof \Closure) {
                        $returnValue = call_user_func_array($returnValue, $parameters);
                    }
                    self::$mockInspectors[$id]->recordInvokation($method, array_values($parameters), $returnValue);
                    return $returnValue;
                }
            };
            $suffix = function ($proxy, $instance, $method, $parameters, $returnValue) use ($id) {
                self::$mockInspectors[$id]->recordInvokation($method, array_values($parameters), $returnValue);
            };
            $mock->setMethodPrefixInterceptor($method->getName(), $prefix);
            $mock->setMethodSuffixInterceptor($method->getName(), $suffix);
        }

        return $mock;
    }
}
