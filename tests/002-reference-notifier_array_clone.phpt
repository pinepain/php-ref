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

$wr2 = clone $wr;

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
array(2) {
  [0]=>
  object(Weak\Reference)#4 (2) {
    ["referent":"Weak\AbstractReference":private]=>
    NULL
    ["notifier":"Weak\AbstractReference":private]=>
    array(2) {
      [0]=>
      *RECURSION*
      [1]=>
      object(Weak\Reference)#3 (2) {
        ["referent":"Weak\AbstractReference":private]=>
        NULL
        ["notifier":"Weak\AbstractReference":private]=>
        *RECURSION*
      }
    }
  }
  [1]=>
  object(Weak\Reference)#3 (2) {
    ["referent":"Weak\AbstractReference":private]=>
    NULL
    ["notifier":"Weak\AbstractReference":private]=>
    array(2) {
      [0]=>
      object(Weak\Reference)#4 (2) {
        ["referent":"Weak\AbstractReference":private]=>
        NULL
        ["notifier":"Weak\AbstractReference":private]=>
        *RECURSION*
      }
      [1]=>
      *RECURSION*
    }
  }
}
array(2) {
  [0]=>
  object(Weak\Reference)#4 (2) {
    ["referent":"Weak\AbstractReference":private]=>
    NULL
    ["notifier":"Weak\AbstractReference":private]=>
    array(2) {
      [0]=>
      *RECURSION*
      [1]=>
      object(Weak\Reference)#3 (2) {
        ["referent":"Weak\AbstractReference":private]=>
        NULL
        ["notifier":"Weak\AbstractReference":private]=>
        *RECURSION*
      }
    }
  }
  [1]=>
  object(Weak\Reference)#3 (2) {
    ["referent":"Weak\AbstractReference":private]=>
    NULL
    ["notifier":"Weak\AbstractReference":private]=>
    array(2) {
      [0]=>
      object(Weak\Reference)#4 (2) {
        ["referent":"Weak\AbstractReference":private]=>
        NULL
        ["notifier":"Weak\AbstractReference":private]=>
        *RECURSION*
      }
      [1]=>
      *RECURSION*
    }
  }
}

EOF
