--TEST--
Ref\functions - test soft* and weak* functions together
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
  object(Ref\WeakReference)#8 (2) refcount(2){
    ["referent":"Ref\AbstractReference":private]=>
    object(stdClass)#2 (0) refcount(3){
    }
    ["notifier":"Ref\AbstractReference":private]=>
    NULL
  }
  [1]=>
  object(Ref\WeakReference)#9 (2) refcount(2){
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
  object(Ref\WeakReference)#10 (2) refcount(2){
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
