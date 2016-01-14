--TEST--
Weak\functions - test weakrefcount() functions after all references death
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

use function Weak\{
    refcounted,
    refcount,
    weakrefcounted,
    weakrefcount,
    weakrefs,
    object_handle
};

use Weak\Reference;

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';


$obj = new stdClass();

$helper->header('Test before any reference created');
$helper->dump_annotated('weakrefcounted($obj)', weakrefcounted($obj));
$helper->dump_annotated('weakrefcount($obj)', weakrefcount($obj));
$helper->dump_annotated('weakrefs($obj)', weakrefs($obj));
$helper->line();


$ref = new Reference($obj);

$helper->header('Test when reference created');
$helper->dump_annotated('weakrefcounted($obj)', weakrefcounted($obj));
$helper->dump_annotated('weakrefcount($obj)', weakrefcount($obj));
$helper->dump_annotated('weakrefs($obj)', weakrefs($obj));
$helper->line();

$ref = null;

$helper->header('Test when reference destroyed');
$helper->dump_annotated('weakrefcounted($obj)', weakrefcounted($obj));
$helper->dump_annotated('weakrefcount($obj)', weakrefcount($obj));
$helper->dump_annotated('weakrefs($obj)', weakrefs($obj));
$helper->line();

?>
--EXPECT--
Test before any reference created:
----------------------------------
weakrefcounted($obj): bool(false)
weakrefcount($obj): int(0)
weakrefs($obj): array(0) refcount(3){
}

Test when reference created:
----------------------------
weakrefcounted($obj): bool(true)
weakrefcount($obj): int(1)
weakrefs($obj): array(1) refcount(3){
  [0]=>
  object(Weak\Reference)#3 (2) refcount(2){
    ["referent":"Weak\Reference":private]=>
    object(stdClass)#2 (0) refcount(2){
    }
    ["notifier":"Weak\Reference":private]=>
    NULL
  }
}

Test when reference destroyed:
------------------------------
weakrefcounted($obj): bool(false)
weakrefcount($obj): int(0)
weakrefs($obj): array(0) refcount(3){
}
