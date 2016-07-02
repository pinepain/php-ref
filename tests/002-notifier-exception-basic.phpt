--TEST--
Weak\NotifierException - basic
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php


try {
  throw new Weak\NotifierException('Test');
} catch (Weak\NotifierException $e) {
  var_dump($e);
}
?>
EOF
--EXPECTF--
object(Weak\NotifierException)#1 (8) {
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
  ["exceptions":"Weak\NotifierException":private]=>
  array(0) {
  }
}
EOF
