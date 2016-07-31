--TEST--
Weak\SoftReference - dump representation, get() and valid() methods
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new stdClass();

$ref = new Weak\SoftReference($obj);

$helper->header('When referent object alive');
$helper->assert('Referent object alive', $ref->get() === $obj);
$helper->assert('Referent object valid', $ref->valid());
$helper->dump($ref);
$helper->space();

$obj = null;


$helper->header('When referent object dead');
$helper->assert('Referent object dead', $ref->get() === null);
$helper->assert('Referent object invalid', $ref->valid(), false);
$helper->dump($ref);
$helper->line();
?>
EOF
--EXPECT--
When referent object alive:
---------------------------
Referent object alive: ok
Referent object valid: ok
object(Weak\SoftReference)#3 (2) refcount(3){
  ["referent":"Weak\AbstractReference":private]=>
  object(stdClass)#2 (0) refcount(2){
  }
  ["notifier":"Weak\AbstractReference":private]=>
  NULL
}


When referent object dead:
--------------------------
Referent object dead: ok
Referent object invalid: ok
object(Weak\SoftReference)#3 (2) refcount(3){
  ["referent":"Weak\AbstractReference":private]=>
  NULL
  ["notifier":"Weak\AbstractReference":private]=>
  NULL
}

EOF
