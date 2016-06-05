<?php

namespace Mockup\Spy;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ObjectInspector
{
    /**
     * @var MethodInspector[]
     */
    private $methodSpies = [];

    /**
     * @param string $method
     * @return MethodInspector
     */
    public function __call($method, array $arguments)
    {
        if (!isset($this->methodSpies[$method])) {
            $this->methodSpies[$method] = new MethodInspector();
        }

        return $this->methodSpies[$method];
    }

    /**
     * @internal
     */
    public function recordInvokation($method, $parameters, $returnValue)
    {
        if (!isset($this->methodSpies[$method])) {
            $this->methodSpies[$method] = new MethodInspector();
        }

        $this->methodSpies[$method]->recordInvokation($parameters, $returnValue);
    }
}
