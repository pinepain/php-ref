--TEST--
Ref\AbstractReference - clone reference
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

use function \Ref\{
    weakrefcount,
    softrefcount,
    weakrefs
};

require '.stubs.php';

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new \stdClass();

$notifier = function () {};

$ar = new \WeakTests\TestAbstractReference($obj, $notifier);

$helper->export_annotated('weakrefcount($obj)', weakrefcount($obj));
$helper->export_annotated('softrefcount($obj)', softrefcount($obj));
var_dump($ar);
$helper->line();

$ar2 = clone $ar;

$helper->assert('Cloned abstract reference matches original', $ar == $ar2);
$helper->assert('Cloned abstract reference does not match original abstract reference strictly', $ar !== $ar2);
$helper->line();

$helper->export_annotated('weakrefcount($obj)', weakrefcount($obj));
$helper->export_annotated('softrefcount($obj)', softrefcount($obj));
var_dump($ar2);
$helper->line();

$helper->assert('Abstract references reported with cloned reference', weakrefs($obj), [$ar, $ar2]);
$helper->line();

$obj = null;
$helper->line();

$helper->assert('Cloned abstract reference matches original', $ar == $ar2);
$helper->assert('Cloned abstract reference does not match original weak reference strictly', $ar !== $ar2);
$helper->line();


?>
EOF
--EXPECT--
weakrefcount($obj): integer: 0
softrefcount($obj): integer: 0
object(WeakTests\TestAbstractReference)#4 (2) {
  ["referent":"Ref\AbstractReference":private]=>
  NULL
  ["notifier":"Ref\AbstractReference":private]=>
  object(Closure)#3 (0) {
  }
}

Cloned abstract reference matches original: ok
Cloned abstract reference does not match original abstract reference strictly: ok

weakrefcount($obj): integer: 0
softrefcount($obj): integer: 0
object(WeakTests\TestAbstractReference)#5 (2) {
  ["referent":"Ref\AbstractReference":private]=>
  NULL
  ["notifier":"Ref\AbstractReference":private]=>
  object(Closure)#3 (0) {
  }
}

Abstract references reported with cloned reference: failed


Cloned abstract reference matches original: ok
Cloned abstract reference does not match original weak reference strictly: ok

EOF
