--TEST--
Ref\WeakReference - array notifier
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new stdClass();

$notifier = [];
$wr = new Ref\WeakReference($obj, $notifier);


var_dump($notifier);

$obj = null;

var_dump($notifier);

$wr = null;

var_dump($notifier);

$helper->line();
?>
EOF
--EXPECT--
array(0) {
}
array(1) {
  [0]=>
  object(Ref\WeakReference)#3 (2) {
    ["referent":"Ref\AbstractReference":private]=>
    NULL
    ["notifier":"Ref\AbstractReference":private]=>
    array(1) {
      [0]=>
      *RECURSION*
    }
  }
}
array(1) {
  [0]=>
  object(Ref\WeakReference)#3 (2) {
    ["referent":"Ref\AbstractReference":private]=>
    NULL
    ["notifier":"Ref\AbstractReference":private]=>
    array(1) {
      [0]=>
      *RECURSION*
    }
  }
}

EOF
