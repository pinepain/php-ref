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

$soft_ref1a = new SoftReference($obj1);
$soft_ref1b = new SoftReference($obj1);
$soft_ref2 = new SoftReference($obj2);

$helper->header('Test Ref\softrefcounted');
$helper->export_annotated('softrefcounted($obj1)', softrefcounted($obj1));
$helper->export_annotated('softrefcounted($obj2)', softrefcounted($obj2));
$helper->export_annotated('softrefcounted($obj3)', softrefcounted($obj3));
$helper->line();

$helper->header('Test Ref\softrefcount');
$helper->export_annotated('softrefcount($obj1)', softrefcount($obj1));
$helper->export_annotated('softrefcount($obj2)', softrefcount($obj2));
$helper->export_annotated('softrefcount($obj3)', softrefcount($obj3));
$helper->line();

$helper->header('Test Ref\softrefs');

$helper->assert('Multiple soft refs reported for object with softrefs()', softrefs($obj1), [$soft_ref1a, $soft_ref1b]);
$helper->dump(softrefs($obj1));
$helper->line();

$helper->assert('Single soft ref reported for object with softrefs()', softrefs($obj2), [$soft_ref2]);
$helper->dump(softrefs($obj2));
$helper->line();

$helper->assert('No soft refs reported for object with softrefs()', softrefs($obj3), []);
$helper->dump(softrefs($obj3));
$helper->line();

?>
--EXPECT--
Test Ref\softrefcounted:
------------------------
softrefcounted($obj1): boolean: true
softrefcounted($obj2): boolean: true
softrefcounted($obj3): boolean: false

Test Ref\softrefcount:
----------------------
softrefcount($obj1): integer: 2
softrefcount($obj2): integer: 1
softrefcount($obj3): integer: 0

Test Ref\softrefs:
------------------
Multiple soft refs reported for object with softrefs(): ok
array(2) refcount(2){
  [0]=>
  object(Ref\SoftReference)#5 (2) refcount(2){
    ["referent":"Ref\AbstractReference":private]=>
    object(stdClass)#2 (0) refcount(3){
    }
    ["notifier":"Ref\AbstractReference":private]=>
    NULL
  }
  [1]=>
  object(Ref\SoftReference)#6 (2) refcount(2){
    ["referent":"Ref\AbstractReference":private]=>
    object(stdClass)#2 (0) refcount(3){
    }
    ["notifier":"Ref\AbstractReference":private]=>
    NULL
  }
}

Single soft ref reported for object with softrefs(): ok
array(1) refcount(2){
  [0]=>
  object(Ref\SoftReference)#7 (2) refcount(2){
    ["referent":"Ref\AbstractReference":private]=>
    object(stdClass)#3 (0) refcount(2){
    }
    ["notifier":"Ref\AbstractReference":private]=>
    NULL
  }
}

No soft refs reported for object with softrefs(): ok
array(0) refcount(2){
}
