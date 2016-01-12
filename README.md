# PHP Weak extension [![Build Status](https://travis-ci.org/pinepain/php-weak.svg)](https://travis-ci.org/pinepain/php-weak)

This extension provides [weak references](https://en.wikipedia.org/wiki/Weak_reference) support for PHP 7 and serves as a toolkit for building other weak data structures. Weak references are particularly useful for avoiding memory-leaks when dealing with listener/callback relationships and object-pools.

## Basic Usage

```php
<?php

use Weak\Reference;

// Create regular target object
$obj = new stdClass();

// Record a weak-reference to it with a callback
$ref = new Reference($obj, function () { echo 'Object destroyed', PHP_EOL; });

// Able to use the reference to reach the original object
assert($ref->isValid();
assert($ref->get() === $obj);

// Object is not prevented from being garbage-collected by PHP engine
$obj = null; // outputs "Object destroyed"

// Can tell that object is no longer available
assert(!$ref->isValid());
assert($ref->get() === null);


```

    
## Docs

This extensions provides:

  - [`class Weak\Reference`](stubs/weak/Reference.php)
  - [`function Weak\refcounted()`](stubs/weak/functions.php)
  - [`function Weak\refcount()`](stubs/weak/functions.php)
  - [`function Weak\weakrefcounted()`](stubs/weak/functions.php)
  - [`function Weak\weakrefcount()`](stubs/weak/functions.php)
  - [`function Weak\weakrefs()`](stubs/weak/functions.php)
  - [`function Weak\object_handle()`](stubs/weak/functions.php)

There are no new INI settings, constants, nor exceptions. 

These new classes and functions are implemented through C code, but [PHP stubs with annotations are available](./stubs/weak) for use with IDEs and code-analysis tools. (See "Stub files for Composer" for a convenient way to integrate them.)

### Known issues and limitations

#### The `spl_object_hash()` of an referenced-object will change

The very first time an object is referenced by a `Weak\Reference`, that object's `spl_object_hash()` will change. The problem will [probably be fixed](https://github.com/php/php-src/pull/1724) with a future release of PHP7. 

#### Callbacks will not be triggered if there are uncaught exceptions in the destruction process

A notification callback may fail to be triggered if:
* The target-object had an unhandled-exception during destruction
* Any other notification-callback for the same object threw an unhandled exception

If you are affected by this limitation and have an idea how to resolve it, please discuss it in php/php-weak#2.

#### Cloning a `Weak\Reference` also clones its notification callback
 
When a `Weak\Reference` is duplicated with `clone`, its callback is also copied, so that when the tracking object is destroyed, the callback will be triggered twice.

```php
<?php

use Weak\Reference;

$obj = new stdClass();

$ref1 = new Reference($obj, function () { echo 'Object destroyed', PHP_EOL; });
$ref2 = clone $ref1;

$obj = null; // outputs "Object destroyed" twice
```

#### Serializing a `Weak\Reference` is prohibited

Weak references only make sense in the context of PHP's memory model, therefore `Weak\Reference` does not support serialization. Attempting to implement the `Serializable` interface will lead to a fatal error. 


## Additional resources

### Stub files for Composer

If you are also using [Composer](https://getcomposer.org/), it is recommended that you add the `pinepain/php-weak-stubs` package as a dev-mode requirement. This provides skeleton definitions and annotations to enable support for auto-completion in your IDE and other code-analysis tools.

    composer require --dev pinepain/php-weak-stubs

### Extra-weak data structures support

To add weak-map support (and probably other data structures), see [php-weak-lib](https://github.com/pinepain/php-weak-lib)
project. If using [Composer](https://getcomposer.org/), you can add them with:

    composer require pinepain/php-weak-lib

## Installation

### Building from sources

    git clone https://github.com/pinepain/php-weak.git
    cd php-weak
    phpize && ./configure && make
    make test

To install the extension globally, run:
    
    # sudo make install

You will need to copy the extension config to your php dir, here is example for Ubuntu with PHP 7 from
[Ondřej Surý's PPA for PHP 7.0](https://launchpad.net/~ondrej/+archive/ubuntu/php-7.0):
   
    # sudo cp provision/php/weak.ini /etc/php/mods-available/
    # sudo phpenmod -v ALL weak
    # sudo service php7.0-fpm restart

### Configuring your project

Once the extension is installed on your PHP platform, no additional configuration is necessary. However, if you  use [Composer](https://getcomposer.org/) and your code is going to require the `weak` extension, you should mark it as a platform-requirement in your [composer.json dependency](https://getcomposer.org/doc/02-libraries.md#platform-packages):

    "require": {
        ...
        "ext-weak": "~0.1.0"
        ...
    }


## Internals

The `Weak\Reference` class is implemented by storing tracked object handlers and then wrapping their original `dtor_obj` handlers with custom ones, with the meta-code:

```php
run_original_dtor_obj($object);

foreach($weak_references as $weak_ref_object_handle => $weak_reference) {
    if ($weak_reference->notify_callback && $no_exception_thrown) {
        $weak_reference->notify_callback($weak_reference);
    }
    
    unset($weak_references[$weak_ref_object_handle]);
}
```

## Development and testing

This extension ships with a Vagrant file which provides a basic environment for development and testing purposes. 
To start it, just type `vagrant up` and then `vagrant ssh` from within the main directory.

Services available out of the box are:

 - Apache2 - on [192.168.33.10:8080](http://192.168.33.102:8080)
 - nginx - on [192.168.33.10:80](http://192.168.33.102:80)

For debugging memory-related problems, valgrind is already installed. To activate it, execute `export TEST_PHP_ARGS=-m` before running tests.

The test suite may prompt to send results to the PHP QA team, which can be disabled by setting the `NO_INTERACTION=1` environment variable. If you run tests in your
own environment, the shell command `export NO_INTERACTION=1` will disable it.

There is a `php7debugzts` directory inside this repo which contains include files for the master php branch (which may be out of date sometimes) to support type-hinting inside your IDE in case you don't have PHP7 include files installed. They are provided only as a convenience, and any serious deveopment should use the real files from the exact PHP version you are developing against.

You may also want to try Rasmus'es [php7dev](https://github.com/rlerdorf/php7dev) box with Debian 8 and ability to switch between large variety of PHP versions.

## References:
 
  [Weak reference on Wikipedia](https://en.wikipedia.org/wiki/Weak_reference)

#### In other languages:

##### Java:

  - [Class `WeakReference<T>`](https://docs.oracle.com/javase/7/docs/api/java/lang/ref/WeakReference.html)
  - [Guidelines for using the Java 2 reference classes](http://www.ibm.com/developerworks/library/j-refs/)
  - [Strong, Soft, Weak and Phantom References](http://neverfear.org/blog/view/150/Strong_Soft_Weak_and_Phantom_References_Java)

##### Python:
    
  - [Weak references in Python 3.5](https://docs.python.org/3.5/library/weakref.html)
  - [Weak references in Python 2](https://docs.python.org/2/library/weakref.html)
  - [PEP 0205 - Weak References](https://www.python.org/dev/peps/pep-0205)

##### .NET

  - [`WeakReference` Class](https://msdn.microsoft.com/en-us/library/system.weakreference.aspx)
  - [`WeakReference<T>` Class](https://msdn.microsoft.com/en-us/library/gg712738%28v=vs.110%29.aspx)

## License

[php-weak](https://github.com/pinepain/php-weak) PHP extension is a free software licensed under the [MIT license](http://opensource.org/licenses/MIT).
