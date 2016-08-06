--TEST--
Ref\WeakReference - array notifier
--SKIPIF--
<?php if (!extension_loaded("ref")) {
    print "skip";
} ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new stdClass();

$notifier = [];
$wr1 = new Ref\WeakReference($obj, $notifier);
$wr2 = new Ref\WeakReference($obj, function () {
    throw new Exception('Testing array notifier reliability');
});
$wr2 = new Ref\WeakReference($obj, $notifier);


var_dump($notifier);

try {
    $obj = null;
} catch (Exception $e) {
    $helper->exception_export($e);
}

var_dump($notifier);

$wr1 = null;

var_dump($notifier);

$helper->line();
?>
EOF
--EXPECT--
array(0) {
}
array(2) {
  [0]=>
  object(Ref\WeakReference)#6 (2) {
    ["referent":"Ref\AbstractReference":private]=>
    NULL
    ["notifier":"Ref\AbstractReference":private]=>
    array(2) {
      [0]=>
      *RECURSION*
      [1]=>
      object(Ref\WeakReference)#3 (2) {
        ["referent":"Ref\AbstractReference":private]=>
        NULL
        ["notifier":"Ref\AbstractReference":private]=>
        *RECURSION*
      }
    }
  }
  [1]=>
  object(Ref\WeakReference)#3 (2) {
    ["referent":"Ref\AbstractReference":private]=>
    NULL
    ["notifier":"Ref\AbstractReference":private]=>
    array(2) {
      [0]=>
      object(Ref\WeakReference)#6 (2) {
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
  object(Ref\WeakReference)#6 (2) {
    ["referent":"Ref\AbstractReference":private]=>
    NULL
    ["notifier":"Ref\AbstractReference":private]=>
    array(2) {
      [0]=>
      *RECURSION*
      [1]=>
      object(Ref\WeakReference)#3 (2) {
        ["referent":"Ref\AbstractReference":private]=>
        NULL
        ["notifier":"Ref\AbstractReference":private]=>
        *RECURSION*
      }
    }
  }
  [1]=>
  object(Ref\WeakReference)#3 (2) {
    ["referent":"Ref\AbstractReference":private]=>
    NULL
    ["notifier":"Ref\AbstractReference":private]=>
    array(2) {
      [0]=>
      object(Ref\WeakReference)#6 (2) {
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
