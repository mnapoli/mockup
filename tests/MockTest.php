<?php

namespace Mockup\Test;

use Mockup\Test\Fixture\Foo;
use Mockup\Test\Fixture\FooInterface;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class MockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function mock_interface()
    {
        /** @var FooInterface $mock */
        $mock = \Mockup\mock(FooInterface::class);

        $this->assertInstanceOf(FooInterface::class, $mock);
        $this->assertNull($mock->foo('bar', 'abc'));
    }

    /**
     * @test
     */
    public function mock_class()
    {
        /** @var Foo $mock */
        $mock = \Mockup\mock(Foo::class);

        $this->assertInstanceOf(Foo::class, $mock);
        $this->assertNull($mock->foo('bar', 'abc'));
    }

    /**
     * @test
     */
    public function spy_object_invokations()
    {
        /** @var Foo $mock */
        $mock = \Mockup\spy(new Foo);
        $mock->foo('bar', 'abc');

        $this->assertEquals(1, \Mockup\inspect($mock)->foo()->invokationCount());
        $this->assertEquals(['bar', 'abc'], \Mockup\inspect($mock)->foo()->parameters());
        $this->assertEquals('bar', \Mockup\inspect($mock)->foo()->returnValue());
    }

    /**
     * @test
     */
    public function spy_interface_invokations()
    {
        /** @var FooInterface $mock */
        $mock = \Mockup\mock(FooInterface::class);
        $mock->foo('bar', 'abc');

        $this->assertEquals(1, \Mockup\inspect($mock)->foo()->invokationCount());
        $this->assertEquals(['bar', 'abc'], \Mockup\inspect($mock)->foo()->parameters());
        $this->assertNull(\Mockup\inspect($mock)->foo()->returnValue());
    }

    /**
     * @test
     */
    public function override_class_method_with_value()
    {
        /** @var Foo $mock */
        $mock = \Mockup\mock(Foo::class, [
            'foo' => 'hello',
        ]);

        $this->assertEquals('hello', $mock->foo('bar', 'abc'));
    }

    /**
     * @test
     */
    public function override_class_method_with_closure()
    {
        /** @var Foo $mock */
        $mock = \Mockup\mock(Foo::class, [
            'foo' => function ($bar, $abc) {
                return $bar . $abc;
            },
        ]);

        $this->assertEquals('barabc', $mock->foo('bar', 'abc'));
    }

    /**
     * @test
     */
    public function spy_overridden_methods()
    {
        /** @var Foo $mock */
        $mock = \Mockup\mock(Foo::class, [
            'foo' => 'hello',
        ]);
        $mock->foo('bar', 'abc');

        $this->assertEquals(1, \Mockup\inspect($mock)->foo()->invokationCount());
        $this->assertEquals(['bar', 'abc'], \Mockup\inspect($mock)->foo()->parameters());
        $this->assertEquals('hello', \Mockup\inspect($mock)->foo()->returnValue());
    }
}
