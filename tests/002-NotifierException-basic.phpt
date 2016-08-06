--TEST--
Ref\NotifierException - basic
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php


try {
  throw new Ref\NotifierException('Test');
} catch (Ref\NotifierException $e) {
  var_dump($e);
}
?>
EOF
--EXPECTF--
object(Ref\NotifierException)#1 (8) {
  ["message":protected]=>
  string(4) "Test"
  ["string":"Exception":private]=>
  string(0) ""
  ["code":protected]=>
  int(0)
  ["file":protected]=>
  string(%d) "%s"
  ["line":protected]=>
  int(5)
  ["trace":"Exception":private]=>
  array(0) {
  }
  ["previous":"Exception":private]=>
  NULL
  ["exceptions":"Ref\NotifierException":private]=>
  array(0) {
  }
}
EOF
