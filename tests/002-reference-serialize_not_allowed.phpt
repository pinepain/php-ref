--TEST--
Weak\Reference - serialize reference
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php
/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new \stdClass();

$wr = new \Weak\Reference($obj, function (\Weak\Reference $reference) {});

$helper->dump($wr);

try {
    $serialized = serialize($wr);
    $helper->dump($serialized);
} catch (\Throwable $e) {
    $helper->exception_export($e);
}

$helper->line();

?>
EOF
--EXPECT--
object(Weak\Reference)#3 (2) refcount(3){
  ["referent":"Weak\AbstractReference":private]=>
  object(stdClass)#2 (0) refcount(2){
  }
  ["notifier":"Weak\AbstractReference":private]=>
  object(Closure)#4 (1) refcount(2){
    ["parameter"]=>
    array(1) refcount(1){
      ["$reference"]=>
      string(10) "<required>" refcount(1)
    }
  }
}
Exception: Serialization of 'Weak\Reference' is not allowed

EOF
