--TEST--
Ref\SoftReference - dump representation of extended reference class
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

require '.stubs.php';

use WeakTests\ExtendedSoftReference;

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new stdClass();

$sr = new ExtendedSoftReference($obj, function (Ref\SoftReference $reference) {}, [42]);

var_dump($sr);
$helper->line();

$obj = null;

var_dump($sr);
$helper->line();
?>
EOF
--EXPECT--
object(WeakTests\ExtendedSoftReference)#3 (3) {
  ["test":"WeakTests\ExtendedSoftReference":private]=>
  array(1) {
    [0]=>
    int(42)
  }
  ["referent":"Ref\AbstractReference":private]=>
  object(stdClass)#2 (0) {
  }
  ["notifier":"Ref\AbstractReference":private]=>
  object(Closure)#4 (1) {
    ["parameter"]=>
    array(1) {
      ["$reference"]=>
      string(10) "<required>"
    }
  }
}

object(WeakTests\ExtendedSoftReference)#3 (3) {
  ["test":"WeakTests\ExtendedSoftReference":private]=>
  array(1) {
    [0]=>
    int(42)
  }
  ["referent":"Ref\AbstractReference":private]=>
  NULL
  ["notifier":"Ref\AbstractReference":private]=>
  object(Closure)#4 (1) {
    ["parameter"]=>
    array(1) {
      ["$reference"]=>
      string(10) "<required>"
    }
  }
}

EOF
