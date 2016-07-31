--TEST--
Weak\SoftReference - array notifier
--SKIPIF--
<?php if (!extension_loaded("weak")) {
    print "skip";
} ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new stdClass();

$notifier = [];
$sr1 = new Weak\SoftReference($obj, $notifier);
$sr2 = new Weak\SoftReference($obj, function () {
    throw new Exception('Testing array notifier reliability');
});
$sr2 = new Weak\SoftReference($obj, $notifier);


var_dump($notifier);

try {
    $obj = null;
} catch (Exception $e) {
    $helper->exception_export($e);
}

var_dump($notifier);

$sr1 = null;

var_dump($notifier);

$helper->line();
?>
EOF
--EXPECT--
array(0) {
}
array(2) {
  [0]=>
  object(Weak\SoftReference)#6 (2) {
    ["referent":"Weak\AbstractReference":private]=>
    NULL
    ["notifier":"Weak\AbstractReference":private]=>
    array(2) {
      [0]=>
      *RECURSION*
      [1]=>
      object(Weak\SoftReference)#3 (2) {
        ["referent":"Weak\AbstractReference":private]=>
        NULL
        ["notifier":"Weak\AbstractReference":private]=>
        *RECURSION*
      }
    }
  }
  [1]=>
  object(Weak\SoftReference)#3 (2) {
    ["referent":"Weak\AbstractReference":private]=>
    NULL
    ["notifier":"Weak\AbstractReference":private]=>
    array(2) {
      [0]=>
      object(Weak\SoftReference)#6 (2) {
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
  object(Weak\SoftReference)#6 (2) {
    ["referent":"Weak\AbstractReference":private]=>
    NULL
    ["notifier":"Weak\AbstractReference":private]=>
    array(2) {
      [0]=>
      *RECURSION*
      [1]=>
      object(Weak\SoftReference)#3 (2) {
        ["referent":"Weak\AbstractReference":private]=>
        NULL
        ["notifier":"Weak\AbstractReference":private]=>
        *RECURSION*
      }
    }
  }
  [1]=>
  object(Weak\SoftReference)#3 (2) {
    ["referent":"Weak\AbstractReference":private]=>
    NULL
    ["notifier":"Weak\AbstractReference":private]=>
    array(2) {
      [0]=>
      object(Weak\SoftReference)#6 (2) {
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
