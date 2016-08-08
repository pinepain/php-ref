--TEST--
Ref\SoftReference - array notifier
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new stdClass();

$notifier = [];
$sr = new Ref\SoftReference($obj, $notifier);

$sr2 = clone $sr;

var_dump($notifier);

$obj = null;

var_dump($notifier);

$sr = null;

var_dump($notifier);

$helper->line();
?>
EOF
--EXPECT--
array(0) {
}
array(2) {
  [0]=>
  object(Ref\SoftReference)#4 (2) {
    ["referent":"Ref\AbstractReference":private]=>
    NULL
    ["notifier":"Ref\AbstractReference":private]=>
    array(2) {
      [0]=>
      *RECURSION*
      [1]=>
      object(Ref\SoftReference)#3 (2) {
        ["referent":"Ref\AbstractReference":private]=>
        NULL
        ["notifier":"Ref\AbstractReference":private]=>
        *RECURSION*
      }
    }
  }
  [1]=>
  object(Ref\SoftReference)#3 (2) {
    ["referent":"Ref\AbstractReference":private]=>
    NULL
    ["notifier":"Ref\AbstractReference":private]=>
    array(2) {
      [0]=>
      object(Ref\SoftReference)#4 (2) {
        ["referent":"Ref\AbstractReference":private]=>
        NULL
        ["notifier":"Ref\AbstractReference":private]=>
        *RECURSION*
      }
      [1]=>
      *RECURSION*
    }
  }
}
array(2) {
  [0]=>
  object(Ref\SoftReference)#4 (2) {
    ["referent":"Ref\AbstractReference":private]=>
    NULL
    ["notifier":"Ref\AbstractReference":private]=>
    array(2) {
      [0]=>
      *RECURSION*
      [1]=>
      object(Ref\SoftReference)#3 (2) {
        ["referent":"Ref\AbstractReference":private]=>
        NULL
        ["notifier":"Ref\AbstractReference":private]=>
        *RECURSION*
      }
    }
  }
  [1]=>
  object(Ref\SoftReference)#3 (2) {
    ["referent":"Ref\AbstractReference":private]=>
    NULL
    ["notifier":"Ref\AbstractReference":private]=>
    array(2) {
      [0]=>
      object(Ref\SoftReference)#4 (2) {
        ["referent":"Ref\AbstractReference":private]=>
        NULL
        ["notifier":"Ref\AbstractReference":private]=>
        *RECURSION*
      }
      [1]=>
      *RECURSION*
    }
  }
}

EOF
