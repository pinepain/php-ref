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


### Notifiers

Notifier can be one of `callable`, `array` or `null` types. `null` notifier denotes no notifier set.

Note that notification happens *after* referent object destruction, so at the time of notification `Weak\Referent::get()` 
will return `null` (unless rare case when object refcount get incremented in destructor, e.g. by storing destructing value
somewhere else).

Callback notifier will not be called if referent object destructor or previous notifier callback throws exception, whether
array notifier get executed even in such cases.


### Cloning
 
When `Weak\Reference` cloned, notifier cloned too, so when tracking object destroyed, both notifier will be called, but
they will be invoked with different `Weak\Reference` objects.

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

Serializing `Weak\Reference` is prohibited. Attempting to implement the `Serializable` interface will lead to a
fatal error.


## Stub files

If you are also using Composer, it is recommended that you add the [php-weak-stub](https://github.com/pinepain/php-weak-stubs)
package as a dev-mode requirement. This provides skeleton definitions and annotations to enable support for auto-completion
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

You will need to copy the extension config to your php dir, here is example for Ubuntu with PHP 7 from
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

`Weak\Reference` class is implemented by storing tracked object handlers and then wrapping it original `dtor_obj` handler 
with custom one, which meta-code is:

```php
run_original_dtor_obj($object);

foreach($weak_references as $weak_ref_object_handle => $weak_reference) {
    if (is_array($weak_reference->notifier)) {
        $weak_reference->notifier[] = $weak_reference;
    } elseif (is_callable($weak_reference->notifier) && $no_exception_thrown) {
        $weak_reference->notifier($weak_reference);
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

[php-weak](https://github.com/pinepain/php-weak) PHP extension is licensed under the [MIT license](http://opensource.org/licenses/MIT).
