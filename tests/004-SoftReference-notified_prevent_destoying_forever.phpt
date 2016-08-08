--TEST--
Ref\SoftReference - prevent original object from being destroyed forever
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

use function \Ref\{
    is_obj_destructor_called
};

require '.stubs.php';

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';


$obj = new \WeakTests\TrackingDtor();
$obj_copy = null;

$sr = new Ref\SoftReference($obj, function (Ref\SoftReference $reference) use (&$obj, &$obj_copy, &$helper) {
    $helper->assert('Notifier called', true);
    $helper->assert('Notifier get 1 argument', sizeof(func_get_args()) === 1);
    $helper->assert('Notifier get soft reference as it argument', $reference instanceof Ref\SoftReference);
    $helper->assert('Original object is null', null === $obj);
    $helper->assert('Soft reference in notifier is not null', null !== $reference->get());
    $helper->assert('Soft reference in notifier points to original object', $reference->get() instanceof \WeakTests\TrackingDtor);
    $helper->space();

    $obj_copy = $reference->get();
});

$helper->header('When referent object alive');
$helper->assert('Referent object alive', $sr->get() === $obj);

$helper->line();
$helper->dump($sr);
$helper->space();

$obj = null;

$helper->header('When referent object was nullified but reference to it stored from notifier');
$helper->assert('Original variable holds null', null === $obj);
$helper->assert('Object copy is not null', null !== $obj_copy);
$helper->assert('Object copy dtor was not called', false === is_obj_destructor_called($obj_copy));
$helper->assert('Referent object alive', $sr->get() === $obj_copy);

$helper->line();
$helper->dump($sr);
$helper->line();

$obj_copy = null;

?>
EOF
--EXPECT--
When referent object alive:
---------------------------
Referent object alive: ok

object(Ref\SoftReference)#3 (2) refcount(3){
  ["referent":"Ref\AbstractReference":private]=>
  object(WeakTests\TrackingDtor)#2 (0) refcount(2){
  }
  ["notifier":"Ref\AbstractReference":private]=>
  object(Closure)#4 (2) refcount(2){
    ["static"]=>
    array(3) refcount(1){
      ["obj"]=>
      &object(WeakTests\TrackingDtor)#2 (0) refcount(2){
      }
      ["obj_copy"]=>
      &NULL
      ["helper"]=>
      &object(Testsuite)#1 (0) refcount(2){
      }
    }
    ["parameter"]=>
    array(1) refcount(1){
      ["$reference"]=>
      string(10) "<required>" refcount(1)
    }
  }
}


Notifier called: ok
Notifier get 1 argument: ok
Notifier get soft reference as it argument: ok
Original object is null: ok
Soft reference in notifier is not null: ok
Soft reference in notifier points to original object: ok


When referent object was nullified but reference to it stored from notifier:
----------------------------------------------------------------------------
Original variable holds null: ok
Object copy is not null: ok
Object copy dtor was not called: ok
Referent object alive: ok

object(Ref\SoftReference)#3 (2) refcount(3){
  ["referent":"Ref\AbstractReference":private]=>
  object(WeakTests\TrackingDtor)#2 (0) refcount(2){
  }
  ["notifier":"Ref\AbstractReference":private]=>
  object(Closure)#4 (2) refcount(2){
    ["static"]=>
    array(3) refcount(1){
      ["obj"]=>
      &NULL
      ["obj_copy"]=>
      &object(WeakTests\TrackingDtor)#2 (0) refcount(2){
      }
      ["helper"]=>
      &object(Testsuite)#1 (0) refcount(2){
      }
    }
    ["parameter"]=>
    array(1) refcount(1){
      ["$reference"]=>
      string(10) "<required>" refcount(1)
    }
  }
}

Notifier called: ok
Notifier get 1 argument: ok
Notifier get soft reference as it argument: ok
Original object is null: ok
Soft reference in notifier is not null: ok
Soft reference in notifier points to original object: ok


EOF
WeakTests\TrackingDtor's destructor called
