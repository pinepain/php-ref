--TEST--
Weak\Reference - dump representation of extended reference class
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

require '.stubs.php';

use WeakTests\ExtendedReference;

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new stdClass();

$wr = new ExtendedReference($obj, function (Weak\Reference $reference) {}, [42]);

$helper->dump($wr);
$helper->line();

$obj = null;

$helper->dump($wr);
$helper->line();
?>
EOF
--EXPECT--
object(WeakTests\ExtendedReference)#3 (3) refcount(3){
  ["test":"WeakTests\ExtendedReference":private]=>
  array(1) refcount(2){
    [0]=>
    int(42)
  }
  ["referent":"Weak\Reference":private]=>
  object(stdClass)#2 (0) refcount(2){
  }
  ["notifier":"Weak\Reference":private]=>
  object(Closure)#4 (1) refcount(2){
    ["parameter"]=>
    array(1) refcount(1){
      ["$reference"]=>
      string(10) "<required>" refcount(1)
    }
  }
}

object(WeakTests\ExtendedReference)#3 (3) refcount(3){
  ["test":"WeakTests\ExtendedReference":private]=>
  array(1) refcount(2){
    [0]=>
    int(42)
  }
  ["referent":"Weak\Reference":private]=>
  NULL
  ["notifier":"Weak\Reference":private]=>
  object(Closure)#4 (1) refcount(2){
    ["parameter"]=>
    array(1) refcount(1){
      ["$reference"]=>
      string(10) "<required>" refcount(1)
    }
  }
}

EOF
