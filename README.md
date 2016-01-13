# PHP Weak extension [![Build Status](https://travis-ci.org/pinepain/php-weak.svg)](https://travis-ci.org/pinepain/php-weak)

This extension provides [weak references](https://en.wikipedia.org/wiki/Weak_reference) support for PHP 7 and serves
as a ground for other weak data structures.

## Usage

```php
<?php

use Weak\Reference;

$obj = new stdClass();

$ref = new Reference($obj, function () { echo 'Object destroyed', PHP_EOL; });

$obj = null; // outputs "Object destroyed"
```

    
## Docs

This extension adds `Weak` namespace and all entities are created inside it.

There are no INI setting, constants or exceptions provided by this extension.

Brief docs about [`Weak\Reference` class](./stubs/weak/Reference.php) and [functions](./stubs/weak/functions.php)
may be seen in [stub files](./stubs/weak).

Short list if what provided by this extension is:

  - `class Weak\Reference`
  - `function Weak\refcounted()`
  - `function Weak\refcount()`
  - `function Weak\weakrefcounted()`
  - `function Weak\weakrefcount()`
  - `function Weak\weakrefs()`
  - `function Weak\object_handle()`

### Special cases

#### Notify callback will not be called if any uncatch exception thrown during object destructing process before it.

If any exception in referent object destructor or other weak references notifier callback (if any) during
object destructing process thrown, next notify callback will not be called.
 
Internally, `Weak\Reference`'s notify callback may be considered as a destructor extension, something like extending object
destructor and call notify callbacks one by one in a row, if any. 

Anyway, we assume that exception in destructors are fatal and the only way they should be handled is fail-fast approach.
 
If you fall down under this limitation and have to try-catch object destructing, pleas, fill an issue and we'll discuss
how this issue may be solved. One of pre-release features was storing destructed object's weak reference in some array
because storing into array is completely internals process and can not be normally interrupted during standard destructor
execution (we don't count `die()` before calling notify callback at all.

#### Object's `spl_object_hash()` hash changes after `Weak\Reference` created for it (PHP <= 7.0.2).

Internally `Weak\Reference` changes object handlers pointer, so `spl_object_hash()` function will provide different hash
for the same object before and after weak reference created for it (actually, hashes will differ only in second part -
in last 16 characters, first 16 chars is object handle hash, and the last 16 is object handlers pointer hash).
This is expected and after weak reference for object created it handlers will not be changed by this extension back, so
hash will not be changed by this extension too.

This has being fixed in PHP > 7.0.2.

#### Comparing two `Weak\Reference` compares only whether they point to the same object.

When you compare two `Weak\Reference` notify callback ignored and only referent objects are compared:

```php
<?php

use Weak\Reference;

$obj = new stdClass();

$ref1 = new Reference($obj);
$ref2 = new Reference($obj, function () {});

var_dump($ref1 == $ref2); // bool(true)
```

Strict comparison works as always:

```php
var_dump($ref1 === $ref2); // bool(false)
```

#### Cloning `Weak\Reference` clones notify callback too.
 
When `Weak\Reference` cloned, notify callback cloned too, so when tracking object destroyed, both notify callbacks (which 
will be the same) will be called:

```php
<?php

use Weak\Reference;

$obj = new stdClass();

$ref1 = new Reference($obj, function () { echo 'Object destroyed', PHP_EOL; });
$ref2 = clone $ref1;

$obj = null; // outputs "Object destroyed" twice
```

#### Serializing of `Weak\Reference` is not allowed.

`Weak\Reference` serializing/unserializing behavior is not allow. Implementing `Serializable` interface will lead to 
fatal error.


## Stub files

Stub files provided as a git subsplit into [php-weak-stub](https://github.com/pinepain/php-weak-stubs) read-only
repository, so you may want to require them with composer to have nice type hinting in your IDE:

    composer require --dev php-weak-stubs


## Extra weak data structures support

To add weak map support (and probably other data structures), see [php-weak-lib](https://github.com/pinepain/php-weak-lib)
project, or just run

    composer require php-weak-lib

to add it to your project.


## Installation

### Building from sources

    git clone https://github.com/pinepain/php-weak.git
    cd php-weak
    phpize && ./configure && make
    make test

To install globally your extension run 
    
    # sudo make install

and copy extension config to your php dir, here is example for Ubuntu with PHP 7 from
[Ondřej Surý's PPA for PHP 7.0](https://launchpad.net/~ondrej/+archive/ubuntu/php-7.0):
   
    # sudo cp provision/php/weak.ini /etc/php/mods-available/
    # sudo phpenmod -v ALL weak
    # sudo service php7.0-fpm restart

You may also want to add php-weak extension as a [composer.json dependency](https://getcomposer.org/doc/02-libraries.md#platform-packages):

    "require": {
        ...
        "ext-weak": "~0.1.0"
        ...
    }


## Internals

`Weak\Reference` class implemented by storing tracked object handlers and then wrapping it original `dtor_obj` handler 
with custom one, which meta-code is:

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

This extension shipped with Vagrant file which provides basic environment for development and testing purposes. 
To start it, just type `vagrant up` and then `vagrant ssh` in php-weak directory.

Services available out of the box are:

 - Apache2 - on [192.168.33.10:8080](http://192.168.33.102:8080)
 - nginx - on [192.168.33.10:80](http://192.168.33.102:80)

For plumbing memory-related problems there are valgrind, to activate it, execute `export TEST_PHP_ARGS=-m` before running tests.

To prevent asking test suite to send results to PHP QA team, `NO_INTERACTION=1` env variable is set. If run tests in your
own environment, just execute `export NO_INTERACTION=1` to mute that reporting.

You may also want to try Rasmus'es [php7dev](https://github.com/rlerdorf/php7dev) box with Debian 8 and ability to switch
between large variety of PHP versions.

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
