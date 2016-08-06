--TEST--
Ref\WeakReference - clone reference
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

require '.stubs.php';

use WeakTests\ExtendedWeakReference;

use function \Ref\{
    weakrefcount,
    weakrefs
};

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new \stdClass();


$notifier = function (Ref\WeakReference $ref) {
    echo 'Notified: ';
    var_dump($ref);
};

$wr = new ExtendedWeakReference($obj, $notifier, [42]);

var_dump($wr);
$helper->line();

$wr2 = clone $wr;

var_dump($wr2);
$helper->line();


?>
EOF
--EXPECT--
object(WeakTests\ExtendedWeakReference)#4 (3) {
  ["test":"WeakTests\ExtendedWeakReference":private]=>
  array(1) {
    [0]=>
    int(42)
  }
  ["referent":"Ref\AbstractReference":private]=>
  object(stdClass)#2 (0) {
  }
  ["notifier":"Ref\AbstractReference":private]=>
  object(Closure)#3 (1) {
    ["parameter"]=>
    array(1) {
      ["$ref"]=>
      string(10) "<required>"
    }
  }
}

object(WeakTests\ExtendedWeakReference)#5 (3) {
  ["test":"WeakTests\ExtendedWeakReference":private]=>
  array(1) {
    [0]=>
    int(42)
  }
  ["referent":"Ref\AbstractReference":private]=>
  object(stdClass)#2 (0) {
  }
  ["notifier":"Ref\AbstractReference":private]=>
  object(Closure)#3 (1) {
    ["parameter"]=>
    array(1) {
      ["$ref"]=>
      string(10) "<required>"
    }
  }
}

EOF
