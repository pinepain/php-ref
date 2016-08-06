--TEST--
Ref\functions - test functions
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

use function Ref\{
    refcounted,
    refcount,
    softrefcounted,
    softrefcount,
    softrefs,
    weakrefcounted,
    weakrefcount,
    weakrefs,
    object_handle,
    is_obj_destructor_called
};

use Ref\WeakReference;
use Ref\SoftReference;

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';


$obj1 = new stdClass();
$obj2 = new stdClass();
$obj3 = new stdClass();

$test = $obj1;

$helper->header('Test Ref\refcounted');
$helper->export_annotated('refcounted($obj1)', refcounted($obj1));
$helper->export_annotated('refcounted($obj2)', refcounted($obj2));
$helper->export_annotated('refcounted(new stdClass())', refcounted(new stdClass()));
// in zts strings are refcounted, in non-zts - not
$helper->export_annotated('refcounted(null)', refcounted(null));
$helper->export_annotated('refcounted(42)', refcounted(42));
$helper->line();

$helper->header('Test Ref\refcount');
$helper->export_annotated('refcount($obj1)', refcount($obj1));
$helper->export_annotated('refcount($obj2)', refcount($obj2));
$helper->export_annotated('refcount(new stdClass())', refcount(new stdClass()));
$helper->export_annotated('refcount(null)', refcount(null));
$helper->export_annotated('refcount(42)', refcount(42));
$helper->line();



$weak_ref1a = new WeakReference($obj1);
$weak_ref1b = new WeakReference($obj1);
$weak_ref2 = new WeakReference($obj2);


$helper->header('Test Ref\weakrefcounted');
$helper->export_annotated('weakrefcounted($obj1)', weakrefcounted($obj1));
$helper->export_annotated('weakrefcounted($obj2)', weakrefcounted($obj2));
$helper->export_annotated('weakrefcounted($obj3)', weakrefcounted($obj3));
$helper->line();

$helper->header('Test Ref\weakrefcount');
$helper->export_annotated('weakrefcount($obj1)', weakrefcount($obj1));
$helper->export_annotated('weakrefcount($obj2)', weakrefcount($obj2));
$helper->export_annotated('weakrefcount($obj3)', weakrefcount($obj3));
$helper->line();


$helper->header('Test Ref\weakrefs');

$helper->assert('Multiple weak refs reported for object with weakrefs()', weakrefs($obj1), [$weak_ref1a, $weak_ref1b]);
$helper->dump(weakrefs($obj1));
$helper->line();

$helper->assert('Single weak ref reported for object with weakrefs()', weakrefs($obj2), [$weak_ref2]);
$helper->dump(weakrefs($obj2));
$helper->line();

$helper->assert('No weak refs reported for object with weakrefs()', weakrefs($obj3), []);
$helper->dump(weakrefs($obj3));
$helper->line();


$helper->header('Test Ref\object_handle');
$helper->export_annotated('object_handle($obj1)', object_handle($obj1));
$helper->export_annotated('object_handle($obj2)', object_handle($obj2));
$helper->line();


$helper->header('Test Ref\is_obj_destructor_called');

class ObjectWithSoftDestructor {

  private $external;

  public function __construct(&$external)
  {
    $this->external = &$external;
  }

  public function __destruct()
  {
    echo __METHOD__ . ' called', PHP_EOL;
    $this->external = $this;
  }
}

$external = null;
$obj = new ObjectWithSoftDestructor($external);

$helper->export_annotated('is_obj_destructor_called($obj)', is_obj_destructor_called($obj));
$obj = null;
$helper->assert('Object stored to external value during destructor call', is_object($external));
$helper->export_annotated('is_obj_destructor_called($external)', is_obj_destructor_called($external));

?>
--EXPECT--
Test Ref\refcounted:
--------------------
refcounted($obj1): boolean: true
refcounted($obj2): boolean: true
refcounted(new stdClass()): boolean: true
refcounted(null): boolean: false
refcounted(42): boolean: false

Test Ref\refcount:
------------------
refcount($obj1): integer: 2
refcount($obj2): integer: 1
refcount(new stdClass()): integer: 0
refcount(null): integer: 0
refcount(42): integer: 0

Test Ref\weakrefcounted:
------------------------
weakrefcounted($obj1): boolean: true
weakrefcounted($obj2): boolean: true
weakrefcounted($obj3): boolean: false

Test Ref\weakrefcount:
----------------------
weakrefcount($obj1): integer: 2
weakrefcount($obj2): integer: 1
weakrefcount($obj3): integer: 0

Test Ref\weakrefs:
------------------
Multiple weak refs reported for object with weakrefs(): ok
array(2) refcount(2){
  [0]=>
  object(Ref\WeakReference)#5 (2) refcount(2){
    ["referent":"Ref\AbstractReference":private]=>
    object(stdClass)#2 (0) refcount(3){
    }
    ["notifier":"Ref\AbstractReference":private]=>
    NULL
  }
  [1]=>
  object(Ref\WeakReference)#6 (2) refcount(2){
    ["referent":"Ref\AbstractReference":private]=>
    object(stdClass)#2 (0) refcount(3){
    }
    ["notifier":"Ref\AbstractReference":private]=>
    NULL
  }
}

Single weak ref reported for object with weakrefs(): ok
array(1) refcount(2){
  [0]=>
  object(Ref\WeakReference)#7 (2) refcount(2){
    ["referent":"Ref\AbstractReference":private]=>
    object(stdClass)#3 (0) refcount(2){
    }
    ["notifier":"Ref\AbstractReference":private]=>
    NULL
  }
}

No weak refs reported for object with weakrefs(): ok
array(0) refcount(2){
}

Test Ref\object_handle:
-----------------------
object_handle($obj1): integer: 2
object_handle($obj2): integer: 3

Test Ref\is_obj_destructor_called:
----------------------------------
is_obj_destructor_called($obj): boolean: false
ObjectWithSoftDestructor::__destruct called
Object stored to external value during destructor call: ok
is_obj_destructor_called($external): boolean: true
