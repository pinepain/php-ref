--TEST--
Ref\AbstractReference - dump representation, get() and valid() methods
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

require '.stubs.php';

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new stdClass();

try {
    new Ref\AbstractReference($obj);
} catch (Throwable $e) {
    $helper->exception_export($e);
    $helper->space();
}


$ref = new \WeakTests\TestAbstractReference($obj);

$helper->header('When referent object alive');
$helper->assert('Referent object dead', $ref->get() === $obj);
$helper->assert('Referent object invalid', $ref->valid(), false);
$helper->dump($ref);
$helper->space();

$obj = null;


$helper->header('When referent object dead');
$helper->assert('Referent object dead', $ref->get() === $obj);
$helper->assert('Referent object invalid', $ref->valid(), false);
$helper->dump($ref);
$helper->line();



?>
--EXPECT--
Error: Cannot instantiate abstract class Ref\AbstractReference


When referent object alive:
---------------------------
Referent object dead: failed
Referent object invalid: ok
object(WeakTests\TestAbstractReference)#4 (2) refcount(3){
  ["referent":"Ref\AbstractReference":private]=>
  NULL
  ["notifier":"Ref\AbstractReference":private]=>
  NULL
}


When referent object dead:
--------------------------
Referent object dead: ok
Referent object invalid: ok
object(WeakTests\TestAbstractReference)#4 (2) refcount(3){
  ["referent":"Ref\AbstractReference":private]=>
  NULL
  ["notifier":"Ref\AbstractReference":private]=>
  NULL
}
