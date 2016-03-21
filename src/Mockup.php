<?php

namespace Mockup;

use ProxyManager\Factory\AccessInterceptorValueHolderFactory;
use ProxyManager\Factory\NullObjectFactory;
use ProxyManager\Proxy\ProxyInterface;
use ReflectionClass;
use ReflectionMethod;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Mockup
{
    /**
     * @var ProxyInterface[]
     */
    private static $mocks = [];

    /**
     * @var array
     */
    private static $invokations = [];

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

    public static function invokationCount($mock, $method)
    {
        return count(self::getInvokations($mock)[$method]);
    }

    public static function parameters($mock, $method, $invokation = 0)
    {
        return self::getInvokations($mock)[$method][$invokation]['parameters'];
    }

    public static function returnValue($mock, $method, $invokation = 0)
    {
        return self::getInvokations($mock)[$method][$invokation]['return'];
    }

    private static function spyInvokations($object, array $methodOverrides, ReflectionClass $reflection)
    {
        $factory = new AccessInterceptorValueHolderFactory;
        $mock = $factory->createProxy($object);
        $id = spl_object_hash($mock);
        self::$mocks[$id] = $mock;

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isStatic()) {
                continue;
            }
            $prefix = function ($proxy, $instance, $method, $parameters, &$returnEarly) use ($id, $methodOverrides) {
                if (array_key_exists($method, $methodOverrides)) {
                    $returnEarly = true;
                    self::$invokations[$id][$method][] = [
                        'parameters' => array_values($parameters),
                        'return' => $methodOverrides[$method],
                    ];
                    return $methodOverrides[$method];
                }
            };
            $suffix = function ($proxy, $instance, $method, $parameters, $returnValue) use ($id) {
                self::$invokations[$id][$method][] = [
                    'parameters' => array_values($parameters),
                    'return' => $returnValue,
                ];
            };
            $mock->setMethodPrefixInterceptor($method->getName(), $prefix);
            $mock->setMethodSuffixInterceptor($method->getName(), $suffix);
        }

        return $mock;
    }

    /**
     * @return array
     */
    private static function getInvokations(ProxyInterface $mock)
    {
        $id = spl_object_hash($mock);

        if (!isset(self::$invokations[$id])) {
            return [];
        }

        return self::$invokations[$id];
    }
}
