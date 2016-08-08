--TEST--
Ref\SoftReference - referencing closure
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = function($greeting) {
    echo 'Hello, ', $greeting, '!', PHP_EOL;
};

$sr = new Ref\SoftReference($obj, function (Ref\SoftReference $reference) {});

$helper->header('When referent object alive');
$helper->assert('Referent object alive', $sr->get() === $obj);
$helper->dump($sr);
$helper->space();

$obj = null;

$helper->header('When referent object dead');
$helper->assert('Referent object dead', $sr->get() === null);

$helper->dump($sr);

$helper->line();
?>
EOF
--EXPECT--
When referent object alive:
---------------------------
Referent object alive: ok
object(Ref\SoftReference)#3 (2) refcount(3){
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
object(Ref\SoftReference)#3 (2) refcount(3){
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
