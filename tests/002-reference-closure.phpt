--TEST--
Weak\Reference - referencing closure
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = function($greeting) {
    echo 'Hello, ', $greeting, '!', PHP_EOL;
};

$wr = new Weak\Reference($obj, function (Weak\Reference $reference) {});

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
object(Weak\Reference)#3 (2) refcount(3){
  ["referent":"Weak\Reference":private]=>
  object(Closure)#2 (1) refcount(2){
    ["parameter"]=>
    array(1) refcount(1){
      ["$greeting"]=>
      string(10) "<required>" refcount(1)
    }
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


When referent object dead:
--------------------------
Referent object dead: ok
object(Weak\Reference)#3 (2) refcount(3){
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
