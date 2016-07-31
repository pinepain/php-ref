# PHP Weak extension

[![Build Status](https://travis-ci.org/pinepain/php-weak.svg)](https://travis-ci.org/pinepain/php-weak)
[![Windows Build status](https://ci.appveyor.com/api/projects/status/7r07eydi6c3lj36a/branch/master?svg=true)](https://ci.appveyor.com/project/pinepain/php-weak)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/pinepain/php-weak/master/LICENSE)

This extension adds [Soft Reference](https://en.wikipedia.org/wiki/Weak_reference) and
[Weak References](https://en.wikipedia.org/wiki/Weak_reference) to PHP 7 and may serve as a ground for other
data structures that require advanced referencing model.


## Usage

```php
<?php

use Weak\Reference;
use Weak\SoftReference;

$obj = new class {
    public function __destruct() {
        echo 'Destructor called', PHP_EOL;
    }
};

$softref = new SoftReference($obj, function () { echo 'Object will be destroyed', PHP_EOL; });
$weakref = new Reference($obj, function () { echo 'Object destroyed', PHP_EOL; });

$obj = null; // outputs "Object will be destroyed", "Destructor called", "Object destroyed" in this specific order.
```

    
## Docs

This extension adds `Weak` namespace and all entities are created inside it.

There are no INI setting or constants provided by this extension.

Brief docs about classes and [functions](./stubs/weak/functions.php)
may be seen in [stub files](./stubs/weak).

Short list if what provided by this extension is:

  - `abstract class Weak\AbstractReference` *may not be subclassed directly* [doc](./stubs/weak/AbstractReference.php)
  - `class Weak\SoftReference extends AbstractReference` [doc](./stubs/weak/SoftReference.php)
  - `class Weak\Reference extends AbstractReference` [doc](./stubs/weak/Reference.php)
  - `class Weak\NotifierException extend Exception` [doc](./stubs/weak/NotifierException.php)
  - `function Weak\refcounted()`
  - `function Weak\refcount()`
  - `function Weak\softrefcounted()`
  - `function Weak\softrefcount()`
  - `function Weak\softrefs()`
  - `function Weak\weakrefcounted()`
  - `function Weak\weakrefcount()`
  - `function Weak\weakrefs()`
  - `function Weak\object_handle()`
  - `function Weak\is_obj_destructor_called()`

### References

There are two type of reference provided by this extension: `SoftReference` and `Reference`. The main difference is that
`SoftReference` call it notifier before referent object will be destructed which allows to prevent object be destroyed,
while `Reference` call it notifier after referent object was destructed.

Note: What this extension provides aren't quite actual soft and weak references, but it comes close for most use cases.

### Notifiers

Notifier can be one of `callable`, `array` or `null` types. `null` notifier denotes no notifier set.

Note that notification happens *after* referent object destruction, so at the time of notification `Weak\Referent::get()` 
will return `null` (unless rare case when object refcount get incremented in destructor, e.g. by storing destructing value
somewhere else).

If object destructor or one or more notifiers throw exception, all further notifier callbacks will be called as if
that exception was thrown inside `try-catch` block. In case one or more exceptions were thrown, `Weak\NotifierException`
will be thrown and all thrown exceptions will be available via `Weak\NotifierException::getExceptions()` method.


### Cloning
 
When reference is cloned, notifier is cloned too, so when tracked object destroyed, both notifier will be called,
but they will be invoked with different reference objects.

```php
<?php

use Weak\Reference;

$obj = new stdClass();

$ref1 = new Reference($obj, function () { echo 'Object destroyed', PHP_EOL; });
$ref2 = clone $ref1;

$obj = null; // outputs "Object destroyed" twice
```

To avoid this you may want to change notifier in `__clone()` method:

```php
<?php

class OwnNotifierReference extends Weak\Reference
{
    public function __clone()
    {
        $this->notifier(function () { echo 'Own notifier called', PHP_EOL;});
    }
}

$obj = new stdClass();

$ref1 = new OwnNotifierReference($obj, function () {
    echo 'Object destroyed', PHP_EOL;
});
$ref2 = clone $ref1;

$obj = null; // outputs "Own notifier called" and then "Object destroyed"
```


### Serializing

Serializing reference object is prohibited. Attempting to implement the `Serializable` interface will lead to a
fatal error.


## Stub files

If you are also using Composer, it is recommended to add the [php-weak-stub](https://github.com/pinepain/php-weak-stubs)
package as a dev-mode requirement. It provides skeleton definitions and annotations to enable support for auto-completion
in your IDE and other code-analysis tools.

    composer require --dev pinepain/php-weak-stubs


## Extra weak data structures support

To add weak map support (and probably other data structures), see [php-weak-lib](https://github.com/pinepain/php-weak-lib)
project, or just run

    composer require pinepain/php-weak-lib

to add it to your project.


## Installation

### Building from sources

    git clone https://github.com/pinepain/php-weak.git
    cd php-weak
    phpize && ./configure && make
    make test

To install extension globally run 
    
    # sudo make install

You will need to copy the extension config to your php dir, here is example for Ubuntu with PHP 7.0 from
[Ondřej Surý's PPA for PHP](https://launchpad.net/~ondrej/+archive/ubuntu/php):
   
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

`Weak\Reference` class is implemented by storing tracked object handlers and then wrapping it original `dtor_obj` handler 
with a custom one, which meta-code is:

```php
$exceptions = [];

foreach($soft_references as $soft_ref_object_handle => $soft_reference) {
    if (is_array($weak_reference->notifier)) {
        $soft_reference->notifier[] = $weak_reference;
    } elseif (is_callable($soft_reference->notifier)) {
        try {
            $soft_reference->notifier($weak_reference);
        } catch(Throwable $e) {
            $exceptions[] = $e;
        }
    }
}

if ($exceptions) {
    throw new Weak\NotifierException('One or more exceptions thrown during notifiers calling', $exceptions);
}

if (refcount($object) == 1) {
    try {
        run_original_dtor_obj($object);
    } catch(Throwable $e) {
        $exceptions[] = $e;
    }

    foreach($weak_references as $weak_ref_object_handle => $weak_reference) {
        if (is_array($weak_reference->notifier)) {
            $weak_reference->notifier[] = $weak_reference;
        } elseif (is_callable($weak_reference->notifier)) {
            try {
                $weak_reference->notifier($weak_reference);
            } catch(Throwable $e) {
                $exceptions[] = $e;
            }
        }
    }

    if ($exceptions) {
        throw new Weak\NotifierException('One or more exceptions thrown during notifiers calling', $exceptions);
    }
} else {
    // required while internally PHP GC mark object as it dtor was called before calling dtor
    mark_object_as_no_destructor_was_called($object);
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
 
  [Soft reference on Wikipedia](https://en.wikipedia.org/wiki/Soft_reference)
  [Weak reference on Wikipedia](https://en.wikipedia.org/wiki/Weak_reference)

#### In other languages:

##### Java:

  - [Class `SoftReference<T>`](https://docs.oracle.com/javase/7/docs/api/java/lang/ref/SoftReference.html)
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

[php-weak](https://github.com/pinepain/php-weak) PHP extension is licensed under the [MIT license](http://opensource.org/licenses/MIT).
