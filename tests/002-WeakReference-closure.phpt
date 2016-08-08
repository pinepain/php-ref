--TEST--
Ref\WeakReference - referencing closure
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = function($greeting) {
    echo 'Hello, ', $greeting, '!', PHP_EOL;
};

$wr = new Ref\WeakReference($obj, function (Ref\WeakReference $reference) {});

$helper->header('When referent object alive');
$helper->assert('Referent object alive', $wr->get() === $obj);
$helper->dump($wr);
$helper->space();

$obj = null;

$helper->header('When referent object dead');
$helper->assert('Referent object dead', $wr->get() === null);

$helper->dump($wr);

$helper->line();
?>
EOF
--EXPECT--
When referent object alive:
---------------------------
Referent object alive: ok
object(Ref\WeakReference)#3 (2) refcount(3){
  ["referent":"Ref\AbstractReference":private]=>
  object(Closure)#2 (1) refcount(2){
    ["parameter"]=>
    array(1) refcount(1){
      ["$greeting"]=>
      string(10) "<required>" refcount(1)
    }
  }
  ["notifier":"Ref\AbstractReference":private]=>
  object(Closure)#4 (1) refcount(2){
    ["parameter"]=>
    array(1) refcount(1){
      ["$reference"]=>
      string(10) "<required>" refcount(1)
    }
  }
}


When referent object dead:
--------------------------
Referent object dead: ok
object(Ref\WeakReference)#3 (2) refcount(3){
  ["referent":"Ref\AbstractReference":private]=>
  NULL
  ["notifier":"Ref\AbstractReference":private]=>
  object(Closure)#4 (1) refcount(2){
    ["parameter"]=>
    array(1) refcount(1){
      ["$reference"]=>
      string(10) "<required>" refcount(1)
    }
  }
}

EOF
