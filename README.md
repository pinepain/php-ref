WARNING: This project is UNSUPPORTED and ABANDONED
==================================================

I'm moving away from PHP world and all my PHP projects going to be abandoned too. Abandoning this project too as I have no intent to continue working on it unless there would be strong request from community and commercial interest. No more updates or documentation will be made. If someone is interested, feels free to contact me using email specified in my GitHub profile.

# PHP Ref extension

[![Build Status](https://travis-ci.org/pinepain/php-ref.svg)](https://travis-ci.org/pinepain/php-ref)
[![Windows Build status](https://ci.appveyor.com/api/projects/status/qr8k54crgfxfxr97/branch/master?svg=true)](https://ci.appveyor.com/project/pinepain/php-ref)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/pinepain/php-ref/master/LICENSE)

This extension adds [Soft Reference](https://en.wikipedia.org/wiki/Soft_reference) and
[Weak References](https://en.wikipedia.org/wiki/Weak_reference) to PHP 7 and may serve as a ground for other
data structures that require advanced referencing model.


**PHP >= 7.2 required.** The last version that supports PHP 7.1 is `v0.5.0`.

### PLEASE READ:

Maintaining this project takes significant amount of time and efforts.
If you like my work and want to show your appreciation, please consider supporting me at https://www.patreon.com/pinepain.

## Usage

```php
<?php

use Ref\WeakReference;
use Ref\SoftReference;

$obj = new class {
    public function __destruct() {
        echo 'Destructor called', PHP_EOL;
    }
};

$softref = new SoftReference($obj, function () { echo 'Object will be destroyed', PHP_EOL; });
$weakref = new WeakReference($obj, function () { echo 'Object destroyed', PHP_EOL; });

$obj = null; // outputs "Object will be destroyed", "Destructor called", "Object destroyed" in this specific order.
```


## Docs

This extension adds `Ref` namespace and all entities are created inside it.

There are no INI setting or constants provided by this extension.

Brief docs about classes and [functions](./stubs/src/functions.php)
may be seen in [stub files](./stubs/src).

Short list if what provided by this extension is:

Classes:
  - `abstract class Ref\AbstractReference` *may not be subclassed directly* ([doc](./stubs/src/AbstractReference.php))
  - `class Ref\SoftReference extends AbstractReference` ([doc](./stubs/srd/SoftReference.php))
  - `class Ref\WeakReference extends AbstractReference` ([doc](./stubs/src/Reference.php))
  - `class Ref\NotifierException extend Exception` ([doc](./stubs/src/NotifierException.php))

Functions ([doc](./stubs/src/functions.php)):
  - `function Ref\refcounted()`
  - `function Ref\refcount()`
  - `function Ref\softrefcounted()`
  - `function Ref\softrefcount()`
  - `function Ref\softrefs()`
  - `function Ref\weakrefcounted()`
  - `function Ref\weakrefcount()`
  - `function Ref\weakrefs()`
  - `function Ref\object_handle()`
  - `function Ref\is_obj_destructor_called()`

### References

There are two type of reference provided by this extension: `SoftReference` and `WeakReference`. The main difference is
that `SoftReference` call it notifier before referent object will be destructed which allows to prevent object be
destroyed, while `WeakReference` call it notifier after referent object was destructed.

Note: What this extension provides aren't quite actual soft and weak references, but it comes close for most use cases.

### Notifier

Notifier can be `callable` or `null`. `null` notifier denotes no notifier set.

Note that notification happens *after* referent object destruction, so at the time of notification `Ref\Referent::get()`
will return `null` (unless rare case when object refcount get incremented in destructor, e.g. by storing destructing value
somewhere else).

If object destructor or one or more notifiers throw exception, all further notifier callbacks will be called as if
that exception was thrown inside `try-catch` block. In case one or more exceptions were thrown, `Ref\NotifierException`
will be thrown and all thrown exceptions will be available via `Ref\NotifierException::getExceptions()` method.


### Cloning

When reference is cloned, notifier is cloned too, so when tracked object destroyed, both notifier will be called,
but they will be invoked with different reference objects.

```php
<?php

use Ref\WeakReference;

$obj = new stdClass();

$ref1 = new WeakReference($obj, function () { echo 'Object destroyed', PHP_EOL; });
$ref2 = clone $ref1;

$obj = null; // outputs "Object destroyed" twice
```

To avoid this you may want to change notifier in `__clone()` method:

```php
<?php

class OwnNotifierReference extends Ref\WeakReference
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

If you are also using Composer, it is recommended to add the [php-ref-stub](https://github.com/pinepain/php-ref-stubs)
package as a dev-mode requirement. It provides skeleton definitions and annotations to enable support for auto-completion
in your IDE and other code-analysis tools.

    composer require --dev pinepain/php-ref-stubs


## Extra weak data structures support

To add object maps support see [php-object-maps](https://github.com/pinepain/php-object-maps) project, or just run

    composer require pinepain/php-object-maps

to add it to your project.


## Installation

### Installing from packages

#### Ubuntu

    $ sudo add-apt-repository -y ppa:ondrej/php
    $ sudo add-apt-repository -y ppa:pinepain/php
    $ sudo apt-get update -y
    $ sudo apt-get install -y php7.2 php-ref
    $ php --ri ref

#### OS X (homebrew)

    $ brew tap homebrew/dupes
    $ brew tap homebrew/php
    $ brew tap pinepain/devtools
    $ brew install php php-ref
    $ php --ri ref

### Building from sources

    git clone https://github.com/pinepain/php-ref.git
    cd php-ref
    phpize && ./configure && make
    make test

To install extension globally run

    # sudo make install

You will need to copy the extension config to your php dir, here is example for Ubuntu with PHP 7.2 from
[Ondřej Surý's PPA for PHP](https://launchpad.net/~ondrej/+archive/ubuntu/php):

    # sudo cp provision/php/ref.ini /etc/php/mods-available/
    # sudo phpenmod -v ALL ref
    # sudo service php7.2-fpm restart

You may also want to add php-ref extension as a [composer.json dependency](https://getcomposer.org/doc/02-libraries.md#platform-packages):

    "require": {
        ...
        "ext-ref": "*"
        ...
    }


## Internals

`Ref\WeakReference` class is implemented by storing tracked object handlers and then wrapping it original `dtor_obj` handler
with a custom one, which meta-code is:

```php
$exceptions = [];

foreach($soft_references as $soft_ref_object_handle => $soft_reference) {
    if (is_callable($soft_reference->notifier)) {
        try {
            $soft_reference->notifier($weak_reference);
        } catch(Throwable $e) {
            $exceptions[] = $e;
        }
    }
}

if ($exceptions) {
    throw new Ref\NotifierException('One or more exceptions thrown during notifiers calling', $exceptions);
}

if (refcount($object) == 1) {
    try {
        run_original_dtor_obj($object);
    } catch(Throwable $e) {
        $exceptions[] = $e;
    }

    foreach($weak_references as $weak_ref_object_handle => $weak_reference) {
        if (is_callable($weak_reference->notifier)) {
            try {
                $weak_reference->notifier($weak_reference);
            } catch(Throwable $e) {
                $exceptions[] = $e;
            }
        }
    }

    if ($exceptions) {
        throw new Ref\NotifierException('One or more exceptions thrown during notifiers calling', $exceptions);
    }
} else {
    // required while internally PHP GC mark object as it dtor was called before calling dtor
    mark_object_as_no_destructor_was_called($object);
}
```

## Development and testing

This extension shipped with Vagrant file which provides basic environment for development and testing purposes.
To start it, just type `vagrant up` and then `vagrant ssh` in php-ref directory.

Services available out of the box are:

 - Apache2 - on [192.168.33.10:8080](http://192.168.33.102:8080)
 - nginx - on [192.168.33.10:80](http://192.168.33.102:80)

For plumbing memory-related problems there are valgrind, to activate it, execute `export TEST_PHP_ARGS=-m` before running tests.

To prevent asking test suite to send results to PHP QA team, `NO_INTERACTION=1` env variable is set. If run tests in your
own environment, just execute `export NO_INTERACTION=1` to mute that reporting.

You may also want to try Rasmus'es [php7dev](https://github.com/rlerdorf/php7dev) box with Debian 8 and ability to switch
between large variety of PHP versions.

## Reference

  - [Soft reference on Wikipedia](https://en.wikipedia.org/wiki/Soft_reference)
  - [Weak reference on Wikipedia](https://en.wikipedia.org/wiki/Weak_reference)

### In other languages:

#### Java:

  - [Class `SoftReference<T>`](https://docs.oracle.com/javase/7/docs/api/java/lang/ref/SoftReference.html)
  - [Class `WeakReference<T>`](https://docs.oracle.com/javase/7/docs/api/java/lang/ref/WeakReference.html)
  - [Guidelines for using the Java 2 reference classes](http://www.ibm.com/developerworks/library/j-refs/)
  - [Strong, Soft, Weak and Phantom References](http://neverfear.org/blog/view/150/Strong_Soft_Weak_and_Phantom_References_Java)

#### Python:

  - [Weak references in Python 3.5](https://docs.python.org/3.5/library/weakref.html)
  - [Weak references in Python 2](https://docs.python.org/2/library/weakref.html)
  - [PEP 0205 - Weak References](https://www.python.org/dev/peps/pep-0205)

#### .NET

  - [`WeakReference` Class](https://msdn.microsoft.com/en-us/library/system.weakreference.aspx)
  - [`WeakReference<T>` Class](https://msdn.microsoft.com/en-us/library/gg712738%28v=vs.110%29.aspx)

## Credits

My thanks to the following people and projects, without whom this extension wouldn't be what it is today.
(Please let me know if I've mistakenly omitted anyone.)

 - [php-weakref](https://github.com/colder/php-weakref) PHP extension which inspired me to write this extension;
 - [Etienne Kneuss](https://github.com/colder), for his profound work on [php-weakref](https://github.com/colder/php-weakref);
 - [Nikita Popov](https://github.com/nikic) for his assistance with confirmin a bug with `spl_object_hash()` being
   inconsistent under certain circumstances;

## License

[php-ref](https://github.com/pinepain/php-ref) PHP extension is licensed under the [MIT license](http://opensource.org/licenses/MIT).
