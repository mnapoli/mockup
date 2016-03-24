<?php

namespace Mockup;

use Mockup\Spy\ObjectInspector;
use ProxyManager\Proxy\ProxyInterface;

/**
 * @return ProxyInterface
 */
function mock($classname, array $methods = []) {
    return Mockup::mock($classname, $methods);
}

/**
 * @return ProxyInterface
 */
function spy($object, array $methods = []) {
    return Mockup::spy($object, $methods);
}

/**
 * @return ObjectInspector
 */
function inspect($mock) {
    return Mockup::inspect($mock);
}
