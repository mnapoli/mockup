# Mockup

Concise mock library for PHP tests.

[![Build Status](https://img.shields.io/travis/mnapoli/mockup.svg?style=flat-square)](https://travis-ci.org/mnapoli/mockup)

## Why?

TODO

## Installation

TODO

## Usage

### Mocks

You can mock a class or an interface:

```php
interface Foo
{
    public function foo($bar);
}

$mock = \Mockup\mock(Foo::class);
$mock->foo();
```

All its methods will do nothing and return `null` ([null object pattern](https://en.wikipedia.org/wiki/Null_Object_pattern)). The mock will implement the interface or extend the class given, as such it will work fine with any type-hint.

You can make some methods return values other than null:

```php
$mock = \Mockup\mock(Foo::class, [
    'foo' => 'hello',
]);

$mock->foo('john'); // hello
```

You can also use a closure to define the new method's body:

```php
$mock = \Mockup\mock(Foo::class, [
    'foo' => function ($bar) {
        return strtoupper('hello ' . $bar);
    }
]);

$mock->foo('john'); // HELLO JOHN
```

### Spies

You can spy calls to an object:

```php
$spy = \Mockup\spy($cache);
$foo->doSomething($spy);

inspect($spy)->method('set')->invokationCount(); // number of calls to $spy->set()
inspect($spy)->method('set')->parameters(0); // parameters provided to the first call to $spy->set()
inspect($spy)->method('set')->returnValue(0); // value returned by the first call to $spy->set()
```

The difference with a mock is that you are spying real calls to a real object. A mock is a [null object](https://en.wikipedia.org/wiki/Null_Object_pattern).

Mockup does not provide assertions or expectations so that you can use the assertion library you prefer.

Every mock object is also a spy, so you can create a mock and spy its method calls:

```php
$mock = \Mockup\mock(CacheInterface::class);
$foo->doSomething($mock);

inspect($spy)->method('set')->invokationCount();
inspect($spy)->method('set')->parameters(0);
inspect($spy)->method('set')->returnValue(0);
```
