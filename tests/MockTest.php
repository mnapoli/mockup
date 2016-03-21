<?php

namespace Mockup\Test;

use Mockup\Mock;
use Mockup\Test\Fixture\FooInterface;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class MockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function mocks_interface()
    {
        $mock = new Mock(FooInterface::class);
        $this->assertInstanceOf(Mock::class, $mock);
        $this->assertNull($mock->get()->foo('bar', 'abc'));
        $this->assertEquals(1, $mock->getInvokationCount('foo'));
        $this->assertEquals(['bar', 'abc'], $mock->getParameters('foo'));
    }
}
