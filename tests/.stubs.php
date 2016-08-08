<?php

namespace WeakTests;

use Ref\WeakReference as WeakReference;

use ArrayAccess;

class TrackingDtor
{
    public function __destruct()
    {
        echo get_class($this), "'s destructor called", PHP_EOL;
    }
}

class WeakReferenceTrackingDtor extends WeakReference
{
    public function __destruct()
    {
        echo get_class($this), "'s destructor called", PHP_EOL;
    }
}

class Test {}


class TestArrayAccess implements ArrayAccess {
    protected $storage = [];

    public function offsetExists($offset)
    {
        echo static::class, '::offsetExists called', PHP_EOL;
        return isset($this->storage[$offset]);
    }

    public function offsetGet($offset)
    {
        echo static::class, '::offsetGet called', PHP_EOL;
        return $this->storage[$offset];
    }

    public function offsetSet($offset, $value)
    {
        echo static::class, '::offsetSet called', PHP_EOL;
        $this->storage[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        echo static::class, '::offsetUnset called', PHP_EOL;
        unset($this->storage[$offset]);
    }
}

class TestArrayAccessCountable extends TestArrayAccess implements \Countable {

    public function count()
    {
        return count($this->storage);
    }
}

class TestProperties {
    public $public = 'public';
    public $public_for_test = 'public for test';
    public $public_for_proxy = 'public for proxy';

    protected $protected = 'protected';

    private $private = 'private';
}

class ExtendedWeakReference extends \Ref\WeakReference {
    /**
     * @var
     */
    private $test = [];

    public function __construct($referent, $notify, $test)
    {
        parent::__construct($referent, $notify);
        $this->test = $test;
    }
}

class ExtendedSoftReference extends \Ref\SoftReference {
    /**
     * @var
     */
    private $test = [];

    public function __construct($referent, $notify, $test)
    {
        parent::__construct($referent, $notify);
        $this->test = $test;
    }
}

class TestAbstractReference extends \Ref\AbstractReference
{
}
