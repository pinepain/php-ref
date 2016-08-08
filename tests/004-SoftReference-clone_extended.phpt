--TEST--
Ref\SoftReference - clone reference
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

require '.stubs.php';

use WeakTests\ExtendedSoftReference;

use function \Ref\{
    softrefcount,
    softrefs
};

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new \stdClass();


$notifier = function (Ref\SoftReference $ref) {
    echo 'Notified: ';
    var_dump($ref);
};

$ref = new ExtendedSoftReference($obj, $notifier, [42]);

var_dump($ref);
$helper->line();

$ref2 = clone $ref;

var_dump($ref2);
$helper->line();


?>
EOF
--EXPECT--
object(WeakTests\ExtendedSoftReference)#4 (3) {
  ["test":"WeakTests\ExtendedSoftReference":private]=>
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

object(WeakTests\ExtendedSoftReference)#5 (3) {
  ["test":"WeakTests\ExtendedSoftReference":private]=>
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
