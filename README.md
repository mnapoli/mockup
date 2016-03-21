# Mockup

Concise mock library for PHP tests.

## Why?

TODO

## Installation

TODO

## Usage

### Mock

You can mock a class or an interface:

```php
interface Foo
{
    public function foo($bar);
}

$mock = \Mockup\mock(Foo::class);
$mock->foo();
```

All its methods will do nothing and return `null`.

You can make some methods return specific values:

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
