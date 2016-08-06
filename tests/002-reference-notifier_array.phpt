--TEST--
Weak\Reference - array notifier
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new stdClass();

$notifier = [];
$wr = new Weak\Reference($obj, $notifier);


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
  object(Weak\Reference)#3 (2) {
    ["referent":"Weak\AbstractReference":private]=>
    NULL
    ["notifier":"Weak\AbstractReference":private]=>
    array(1) {
      [0]=>
      *RECURSION*
    }
  }
}
array(1) {
  [0]=>
  object(Weak\Reference)#3 (2) {
    ["referent":"Weak\AbstractReference":private]=>
    NULL
    ["notifier":"Weak\AbstractReference":private]=>
    array(1) {
      [0]=>
      *RECURSION*
    }
  }
}

EOF
