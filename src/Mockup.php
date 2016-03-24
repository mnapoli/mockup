<?php

namespace Mockup;

use Mockup\Spy\MethodSpy;
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
     * @var MethodSpy[][]
     */
    private static $methodSpies = [];

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
        $id = spl_object_hash($mock);
        return self::$methodSpies[$id][$method]->invokationCount();
    }

    public static function parameters($mock, $method, $invokation = 0)
    {
        $id = spl_object_hash($mock);
        return self::$methodSpies[$id][$method]->parameters($invokation);
    }

    public static function returnValue($mock, $method, $invokation = 0)
    {
        $id = spl_object_hash($mock);
        return self::$methodSpies[$id][$method]->returnValue($invokation);
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

            $methodSpy = new MethodSpy();

            $prefix = function ($proxy, $instance, $method, $parameters, &$returnEarly) use ($id, $methodOverrides, $methodSpy) {
                if (array_key_exists($method, $methodOverrides)) {
                    $returnEarly = true;
                    $returnValue = $methodOverrides[$method];
                    if ($returnValue instanceof \Closure) {
                        $returnValue = call_user_func_array($returnValue, $parameters);
                    }
                    $methodSpy->recordInvokation(array_values($parameters), $returnValue);
                    return $returnValue;
                }
            };
            $suffix = function ($proxy, $instance, $method, $parameters, $returnValue) use ($id, $methodSpy) {
                $methodSpy->recordInvokation(array_values($parameters), $returnValue);
            };
            $mock->setMethodPrefixInterceptor($method->getName(), $prefix);
            $mock->setMethodSuffixInterceptor($method->getName(), $suffix);

            self::$methodSpies[$id][$method->getName()] = $methodSpy;
        }

        return $mock;
    }
}
