--TEST--
Ref\WeakReference - serialize reference
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php
/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new \stdClass();

$wr = new \Ref\WeakReference($obj, function (\Ref\WeakReference $reference) {});

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
object(Ref\WeakReference)#3 (2) refcount(3){
  ["referent":"Ref\AbstractReference":private]=>
  object(stdClass)#2 (0) refcount(2){
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
Exception: Serialization of 'Ref\WeakReference' is not allowed

EOF
