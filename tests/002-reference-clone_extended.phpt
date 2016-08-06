--TEST--
Weak\Reference - clone reference
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

require '.stubs.php';

use WeakTests\ExtendedReference;

use function \Weak\{
    weakrefcount,
    weakrefs
};

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new \stdClass();


$notifier = function (Weak\Reference $ref) {
    echo 'Notified: ';
    var_dump($ref);
};

$wr = new ExtendedReference($obj, $notifier, [42]);

var_dump($wr);
$helper->line();

$wr2 = clone $wr;

var_dump($wr2);
$helper->line();


?>
EOF
--EXPECT--
object(WeakTests\ExtendedReference)#4 (3) {
  ["test":"WeakTests\ExtendedReference":private]=>
  array(1) {
    [0]=>
    int(42)
  }
  ["referent":"Weak\AbstractReference":private]=>
  object(stdClass)#2 (0) {
  }
  ["notifier":"Weak\AbstractReference":private]=>
  object(Closure)#3 (1) {
    ["parameter"]=>
    array(1) {
      ["$ref"]=>
      string(10) "<required>"
    }
  }
}

object(WeakTests\ExtendedReference)#5 (3) {
  ["test":"WeakTests\ExtendedReference":private]=>
  array(1) {
    [0]=>
    int(42)
  }
  ["referent":"Weak\AbstractReference":private]=>
  object(stdClass)#2 (0) {
  }
  ["notifier":"Weak\AbstractReference":private]=>
  object(Closure)#3 (1) {
    ["parameter"]=>
    array(1) {
      ["$ref"]=>
      string(10) "<required>"
    }
  }
}

EOF
