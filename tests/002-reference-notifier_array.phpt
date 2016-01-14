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


$helper->dump($notifier);

$obj = null;

$helper->dump($notifier);

$wr = null;

$helper->dump($notifier);

$helper->line();
?>
EOF
--EXPECT--
array(0) refcount(4){
}
array(1) refcount(4){
  [0]=>
  object(Weak\Reference)#3 (2) refcount(2){
    ["referent":"Weak\Reference":private]=>
    NULL
    ["notifier":"Weak\Reference":private]=>
    array(1) refcount(5){
      [0]=>
      object(Weak\Reference)#3 (2) refcount(2){
        ["referent":"Weak\Reference":private]=>
        NULL
        ["notifier":"Weak\Reference":private]=>
        array(1) refcount(6){
          [0]=>
          object(Weak\Reference)#3 (2) refcount(2){
            ["referent":"Weak\Reference":private]=>
            NULL
            ["notifier":"Weak\Reference":private]=>
            *RECURSION*
          }
        }
      }
    }
  }
}
array(1) refcount(4){
  [0]=>
  object(Weak\Reference)#3 (2) refcount(1){
    ["referent":"Weak\Reference":private]=>
    NULL
    ["notifier":"Weak\Reference":private]=>
    array(1) refcount(5){
      [0]=>
      object(Weak\Reference)#3 (2) refcount(1){
        ["referent":"Weak\Reference":private]=>
        NULL
        ["notifier":"Weak\Reference":private]=>
        array(1) refcount(6){
          [0]=>
          object(Weak\Reference)#3 (2) refcount(1){
            ["referent":"Weak\Reference":private]=>
            NULL
            ["notifier":"Weak\Reference":private]=>
            *RECURSION*
          }
        }
      }
    }
  }
}

EOF
