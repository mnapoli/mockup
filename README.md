# Mockup

Concise mock library for PHP tests.

[![Build Status](https://img.shields.io/travis/mnapoli/mockup.svg?style=flat-square)](https://travis-ci.org/mnapoli/mockup)

## Why?

This mock library is meant to be a simple yet powerful alternative to existing solutions.

- Mockup

```php
$mock = mock(Foo::class, [
    'foo' => 'Hello',
]);
```

- PHPUnit

```php
$mock = $this->getMock(Foo::class, [], [], '', false);
$mock->expect($this->any())
    ->method('foo')
    ->willReturn('Hello');
```

- Prophecy

```php
$prophet = new \Prophecy\Prophet();
$mock = $prophet->prophesize(Foo::class);
$mock->foo()->willReturn('Hello');
$mock = $mock->reveal();
```

- Mockery

```php
$mock = Mockery::mock(Foo::class);
$mock->shouldReceive('foo')
    ->andReturn('Hello');
```

Additionally, Mockup **doesn't include assertions**. Instead of forcing you to learn a specific assertion syntax, complex enough to cover all cases, it lets you use the assertions you already know (PHPUnit, phpspec, …). Here is an example in a PHPUnit test:

```php
// The mock method was called once
$this->assertEquals(1, inspect($mock)->foo()->invokationCount());
```

Read more about spying method calls below.

## Installation

```
composer require --dev mnapoli/mockup
```

## Usage

### Mocks

You can mock a class or an interface:

```php
use function Mockup\mock;

interface Foo
{
    public function foo($bar);
}

$mock = mock(Foo::class);
$mock->foo();
```

All its methods will do nothing and return `null` ([null object pattern](https://en.wikipedia.org/wiki/Null_Object_pattern)). The mock will implement the interface or extend the class given, as such it will work fine with any type-hint.

You can make some methods return values other than null:

```php
$mock = mock(Foo::class, [
    'foo' => 'hello',
]);

$mock->foo('john'); // hello
```

You can also use a closure to define the new method's body:

```php
$mock = mock(Foo::class, [
    'foo' => function ($bar) {
        return strtoupper('hello ' . $bar);
    }
]);

$mock->foo('john'); // HELLO JOHN
```

### Spies

You can spy calls to an object:

```php
use function Mockup\{spy, inspect};

$spy = spy($cache);
$foo->doSomething($spy);

inspect($spy)->set()->invokationCount(); // number of calls to $spy->set()
inspect($spy)->set()->parameters(0); // parameters provided to the first call to $spy->set()
inspect($spy)->set()->returnValue(0); // value returned by the first call to $spy->set()
```

The difference with a mock is that you are spying real calls to a real object. A mock is a [null object](https://en.wikipedia.org/wiki/Null_Object_pattern).

Mockup does not provide assertions or expectations so that you can use the assertion library you prefer.

Every mock object is also a spy, so you can create a mock and spy its method calls:

```php
use function Mockup\{mock, inspect};

$mock = mock(CacheInterface::class);
$foo->doSomething($mock);

inspect($spy)->set()->invokationCount();
inspect($spy)->set()->parameters(0);
inspect($spy)->set()->returnValue(0);
```
