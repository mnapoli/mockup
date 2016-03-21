<?php

namespace Mockup;

use ProxyManager\Factory\AccessInterceptorValueHolderFactory;
use ProxyManager\Proxy\ProxyInterface;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Mock
{
    /**
     * @var ProxyInterface
     */
    private $proxy;

    /**
     * @var array
     */
    private $invokations = [];

    /**
     * @var array
     */
    private $methodOverrides = [];

    public function __construct($object, array $methodOverrides = [], $mock = true)
    {
        $this->methodOverrides = $methodOverrides;

        $reflection = new \ReflectionClass($object);

        $factory = new AccessInterceptorValueHolderFactory;
        $this->proxy = $factory->createProxy($object);

        foreach ($reflection->getMethods() as $method) {
            if (!$method->isPublic() || $method->isStatic()) {
                continue;
            }
            $prefix = function ($proxy, $instance, $method, $parameters, &$returnEarly) use ($mock) {
                if (array_key_exists($method, $this->methodOverrides)) {
                    $returnEarly = true;
                    return $this->methodOverrides[$method];
                }
                if ($mock) {
                    $returnEarly = true;
                }
            };
            $suffix = function ($proxy, $instance, $method, $parameters, $returnValue) {
                $this->invokations[$method][] = [
                    'parameters' => array_values($parameters),
                    'return' => $returnValue,
                ];
            };
            $this->proxy->setMethodPrefixInterceptor($method->getName(), $prefix);
            $this->proxy->setMethodSuffixInterceptor($method->getName(), $suffix);
        }
    }

    public function get()
    {
        return $this->proxy;
    }

    public function getInvokationCount($method)
    {
        return count($this->invokations[$method]);
    }

    public function getParameters($method, $invokation = 0)
    {
        return $this->invokations[$method][$invokation]['parameters'];
    }

    public function getReturnedValue($method, $invokation = 0)
    {
        return $this->invokations[$method][$invokation]['return'];
    }
}
