# PHP Weak extension [![Build Status](https://travis-ci.org/pinepain/php-weak.svg)](https://travis-ci.org/pinepain/php-weak)

This extension provides [weak references](https://en.wikipedia.org/wiki/Weak_reference) support for PHP 7 and serves as a toolkit for building other weak data structures.

## Usage

```php
<?php

use Weak\Reference;

$obj = new stdClass();

$ref = new Reference($obj, function () { echo 'Object destroyed', PHP_EOL; });

$obj = null; // outputs "Object destroyed"
```

    
## Docs

This extensions provides:

  - `class Weak\Reference`
  - `function Weak\refcounted()`
  - `function Weak\refcount()`
  - `function Weak\weakrefcounted()`
  - `function Weak\weakrefcount()`
  - `function Weak\weakrefs()`
  - `function Weak\object_handle()`

There are no new INI settings, constants, nor exceptions. 

These new classes/functions are implemented through C code, but [PHP stubs with annotations are available](./stubs/weak) for use with IDEs and code-analysis tools.

### Known issues and limitations

#### Callbacks will not be triggered if there are uncaught exceptions in the destruction process

A notification callback may fail to be triggered if:
* The target-object had an unhandled-exception during destruction
* Any other notification callback for the same object had an unhandled exception

Internally, `Weak\Reference`'s notification-callback mechanism may be considered as an extension to the target-object's destruction process.
 
If you re affected by this limitation and need to catch an exception from object destruction, please [open a github issue](https://github.com/pinepain/php-weak/issues/new) to discuss how it may be resolved.

#### The target-object's `spl_object_hash()` hash will change each time a `Weak\Reference` created with it

Creating a `Weak\Reference` has a side-effect on on the object that you are making the reference to. Because it changes certain internal PHP pointers, it will alter the `spl_object_hash()` of the object. 

This is expected behavior, and only applies when the very first weak-reference is created around the target-object.

#### Loosely-comparing two `Weak\Reference` compares only whether they point to the same target

When you loosely-compare two `Weak\Reference` objects, the content of their noficiation-callback is ignored, and they will be considered equal if they point to the same target-object. For example:

```php
<?php

use Weak\Reference;

$obj = new stdClass();

$ref1 = new Reference($obj);
$ref2 = new Reference($obj, function () {});

var_dump($ref1 == $ref2); // bool(true)
```

Strict comparison should still work as expected:

```php
var_dump($ref1 === $ref2); // bool(false)
```

#### Cloning a `Weak\Reference` also clones the notification callback
 
When a `Weak\Reference` is duplicated with `clone`, its callback is also copied, so that when tracking object destroyed, the callback will be triggered twice.

```php
<?php

use Weak\Reference;

$obj = new stdClass();

$ref1 = new Reference($obj, function () { echo 'Object destroyed', PHP_EOL; });
$ref2 = clone $ref1;

$obj = null; // outputs "Object destroyed" twice
```

#### Serializing `Weak\Reference` is prohibited

`Weak\Reference` serializing/unserializing behavior is not supported. Attempting to implement the `Serializable` interface will lead to a fatal error.


## Stub files for Composer

If you are also using [Composer](https://getcomposer.org/), it is recommended that you add the `pinepain/php-weak-stubs` package as a dev-mode requirement. This provides skeleton definitions and annotations to enable support for auto-completion in your IDE and other code-analysis tools.

    composer require --dev pinepain/php-weak-stubs

## Extra-weak data structures support

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

## Reference:
 
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
