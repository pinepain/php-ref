--TEST--
Weak\Reference - clone reference
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

use function \Weak\{
    weakrefcount,
    weakrefs
};

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new \stdClass();

$notifier = function (Weak\Reference $ref) use ($helper) {
    echo 'Notified: ';
    $helper->dump($ref);
};

$wr = new \Weak\Reference($obj, $notifier);

$helper->export_annotated('weakrefcount($obj)', weakrefcount($obj));
$helper->dump($wr);
$helper->line();

$wr2 = clone $wr;

$helper->assert('Cloned weak reference matches original', $wr == $wr2);
$helper->assert('Cloned weak reference does not match original weak reference strictly', $wr !== $wr2);
$helper->line();

$helper->export_annotated('weakrefcount($obj)', weakrefcount($obj));
$helper->dump($wr2);
$helper->line();

$helper->assert('Weak references reported with cloned reference', weakrefs($obj), [$wr, $wr2]);
$helper->line();

$obj = null;
$helper->line();

$helper->assert('Cloned weak reference matches original', $wr == $wr2);
$helper->assert('Cloned weak reference does not match original weak reference strictly', $wr !== $wr2);
$helper->line();


?>
EOF
--EXPECT--
weakrefcount($obj): integer: 1
object(Weak\Reference)#4 (2) refcount(3){
  ["referent":"Weak\Reference":private]=>
  object(stdClass)#2 (0) refcount(2){
  }
  ["notifier":"Weak\Reference":private]=>
  object(Closure)#3 (2) refcount(3){
    ["static"]=>
    array(1) refcount(1){
      ["helper"]=>
      object(Testsuite)#1 (0) refcount(4){
      }
    }
    ["parameter"]=>
    array(1) refcount(1){
      ["$ref"]=>
      string(10) "<required>" refcount(1)
    }
  }
}

Cloned weak reference matches original: ok
Cloned weak reference does not match original weak reference strictly: ok

weakrefcount($obj): integer: 2
object(Weak\Reference)#5 (2) refcount(3){
  ["referent":"Weak\Reference":private]=>
  object(stdClass)#2 (0) refcount(2){
  }
  ["notifier":"Weak\Reference":private]=>
  object(Closure)#3 (2) refcount(4){
    ["static"]=>
    array(1) refcount(1){
      ["helper"]=>
      object(Testsuite)#1 (0) refcount(4){
      }
    }
    ["parameter"]=>
    array(1) refcount(1){
      ["$ref"]=>
      string(10) "<required>" refcount(1)
    }
  }
}

Weak references reported with cloned reference: ok

Notified: object(Weak\Reference)#5 (2) refcount(6){
  ["referent":"Weak\Reference":private]=>
  NULL
  ["notifier":"Weak\Reference":private]=>
  object(Closure)#3 (2) refcount(5){
    ["static"]=>
    array(1) refcount(1){
      ["helper"]=>
      object(Testsuite)#1 (0) refcount(5){
      }
    }
    ["parameter"]=>
    array(1) refcount(1){
      ["$ref"]=>
      string(10) "<required>" refcount(1)
    }
  }
}
Notified: object(Weak\Reference)#4 (2) refcount(6){
  ["referent":"Weak\Reference":private]=>
  NULL
  ["notifier":"Weak\Reference":private]=>
  object(Closure)#3 (2) refcount(5){
    ["static"]=>
    array(1) refcount(1){
      ["helper"]=>
      object(Testsuite)#1 (0) refcount(5){
      }
    }
    ["parameter"]=>
    array(1) refcount(1){
      ["$ref"]=>
      string(10) "<required>" refcount(1)
    }
  }
}

Cloned weak reference matches original: ok
Cloned weak reference does not match original weak reference strictly: ok

EOF
