--TEST--
Weak\Reference - dump representation, get() and valid() methods
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new stdClass();

$wr = new Weak\Reference($obj);

$helper->header('When referent object alive');
$helper->assert('Referent object alive', $wr->get() === $obj);
$helper->assert('Referent object valid', $wr->valid());
$helper->dump($wr);
$helper->space();

$obj = null;


$helper->header('When referent object dead');
$helper->assert('Referent object dead', $wr->get() === null);
$helper->assert('Referent object invalid', $wr->valid(), false);
$helper->dump($wr);
$helper->line();
?>
EOF
--EXPECT--
When referent object alive:
---------------------------
Referent object alive: ok
Referent object valid: ok
object(Weak\Reference)#3 (2) refcount(3){
  ["referent":"Weak\Reference":private]=>
  object(stdClass)#2 (0) refcount(2){
  }
  ["notifier":"Weak\Reference":private]=>
  NULL
}


When referent object dead:
--------------------------
Referent object dead: ok
Referent object invalid: ok
object(Weak\Reference)#3 (2) refcount(3){
  ["referent":"Weak\Reference":private]=>
  NULL
  ["notifier":"Weak\Reference":private]=>
  NULL
}

EOF
