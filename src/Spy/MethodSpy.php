<?php

namespace Mockup\Spy;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class MethodSpy
{
    /**
     * @var array
     */
    private $invokations;

    /**
     * @return int
     */
    public function invokationCount()
    {
        return count($this->invokations);
    }

    /**
     * @param int $invokation
     * @return array
     */
    public function parameters($invokation = 0)
    {
        return $this->invokations[$invokation]['parameters'];
    }

    /**
     * @param int $invokation
     * @return mixed
     */
    public function returnValue($invokation = 0)
    {
        return $this->invokations[$invokation]['return'];
    }

    /**
     * @internal
     */
    public function recordInvokation(array $parameters, $returnValue)
    {
        $this->invokations[] = [
            'parameters' => $parameters,
            'return' => $returnValue,
        ];
    }
}
