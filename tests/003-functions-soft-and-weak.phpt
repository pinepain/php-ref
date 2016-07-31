--TEST--
Weak\functions - test soft* and weak* functions together
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

use function Weak\{
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

use Weak\Reference;
use Weak\SoftReference;

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';


$obj1 = new stdClass();
$obj2 = new stdClass();
$obj3 = new stdClass();

$test = $obj1;

$soft_ref1a = new SoftReference($obj1);
$soft_ref1b = new SoftReference($obj1);
$soft_ref2 = new SoftReference($obj2);


$helper->header('Test Weak\softrefcounted');
$helper->export_annotated('softrefcounted($obj1)', softrefcounted($obj1));
$helper->export_annotated('softrefcounted($obj2)', softrefcounted($obj2));
$helper->export_annotated('softrefcounted($obj3)', softrefcounted($obj3));
$helper->line();


$helper->header('Test Weak\softrefcount');
$helper->export_annotated('softrefcount($obj1)', softrefcount($obj1));
$helper->export_annotated('softrefcount($obj2)', softrefcount($obj2));
$helper->export_annotated('softrefcount($obj3)', softrefcount($obj3));
$helper->line();


$helper->header('Test Weak\softrefs');

$helper->assert('Multiple soft refs reported for object with softrefs()', softrefs($obj1), [$soft_ref1a, $soft_ref1b]);
$helper->dump(softrefs($obj1));
$helper->line();

$helper->assert('Single soft ref reported for object with softrefs()', softrefs($obj2), [$soft_ref2]);
$helper->dump(softrefs($obj2));
$helper->line();

$helper->assert('No soft refs reported for object with softrefs()', softrefs($obj3), []);
$helper->dump(softrefs($obj3));
$helper->line();



$weak_ref1a = new Reference($obj1);
$weak_ref1b = new Reference($obj1);
$weak_ref2 = new Reference($obj2);


$helper->header('Test Weak\weakrefcounted');
$helper->export_annotated('weakrefcounted($obj1)', weakrefcounted($obj1));
$helper->export_annotated('weakrefcounted($obj2)', weakrefcounted($obj2));
$helper->export_annotated('weakrefcounted($obj3)', weakrefcounted($obj3));
$helper->line();


$helper->header('Test Weak\weakrefcount');
$helper->export_annotated('weakrefcount($obj1)', weakrefcount($obj1));
$helper->export_annotated('weakrefcount($obj2)', weakrefcount($obj2));
$helper->export_annotated('weakrefcount($obj3)', weakrefcount($obj3));
$helper->line();


$helper->header('Test Weak\weakrefs');

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
Test Weak\softrefcounted:
-------------------------
softrefcounted($obj1): boolean: true
softrefcounted($obj2): boolean: true
softrefcounted($obj3): boolean: false

Test Weak\softrefcount:
-----------------------
softrefcount($obj1): integer: 2
softrefcount($obj2): integer: 1
softrefcount($obj3): integer: 0

Test Weak\softrefs:
-------------------
Multiple soft refs reported for object with softrefs(): ok
array(2) refcount(2){
  [0]=>
  object(Weak\SoftReference)#5 (2) refcount(2){
    ["referent":"Weak\AbstractReference":private]=>
    object(stdClass)#2 (0) refcount(3){
    }
    ["notifier":"Weak\AbstractReference":private]=>
    NULL
  }
  [1]=>
  object(Weak\SoftReference)#6 (2) refcount(2){
    ["referent":"Weak\AbstractReference":private]=>
    object(stdClass)#2 (0) refcount(3){
    }
    ["notifier":"Weak\AbstractReference":private]=>
    NULL
  }
}

Single soft ref reported for object with softrefs(): ok
array(1) refcount(2){
  [0]=>
  object(Weak\SoftReference)#7 (2) refcount(2){
    ["referent":"Weak\AbstractReference":private]=>
    object(stdClass)#3 (0) refcount(2){
    }
    ["notifier":"Weak\AbstractReference":private]=>
    NULL
  }
}

No soft refs reported for object with softrefs(): ok
array(0) refcount(2){
}

Test Weak\weakrefcounted:
-------------------------
weakrefcounted($obj1): boolean: true
weakrefcounted($obj2): boolean: true
weakrefcounted($obj3): boolean: false

Test Weak\weakrefcount:
-----------------------
weakrefcount($obj1): integer: 2
weakrefcount($obj2): integer: 1
weakrefcount($obj3): integer: 0

Test Weak\weakrefs:
-------------------
Multiple weak refs reported for object with weakrefs(): ok
array(2) refcount(2){
  [0]=>
  object(Weak\Reference)#8 (2) refcount(2){
    ["referent":"Weak\AbstractReference":private]=>
    object(stdClass)#2 (0) refcount(3){
    }
    ["notifier":"Weak\AbstractReference":private]=>
    NULL
  }
  [1]=>
  object(Weak\Reference)#9 (2) refcount(2){
    ["referent":"Weak\AbstractReference":private]=>
    object(stdClass)#2 (0) refcount(3){
    }
    ["notifier":"Weak\AbstractReference":private]=>
    NULL
  }
}

Single weak ref reported for object with weakrefs(): ok
array(1) refcount(2){
  [0]=>
  object(Weak\Reference)#10 (2) refcount(2){
    ["referent":"Weak\AbstractReference":private]=>
    object(stdClass)#3 (0) refcount(2){
    }
    ["notifier":"Weak\AbstractReference":private]=>
    NULL
  }
}

No weak refs reported for object with weakrefs(): ok
array(0) refcount(2){
}
