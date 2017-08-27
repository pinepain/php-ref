--TEST--
Ref\WeakReference - clone reference
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

use function \Ref\{
    weakrefcount,
    weakrefs
};

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new \stdClass();

$notifier = function (Ref\WeakReference $ref) use ($helper) {
    echo 'Notified: ';
    $helper->dump($ref);
};

$wr = new \Ref\WeakReference($obj, $notifier);

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
object(Ref\WeakReference)#4 (2) refcount(3){
  ["referent":"Ref\AbstractReference":private]=>
  object(stdClass)#2 (0) refcount(2){
  }
  ["notifier":"Ref\AbstractReference":private]=>
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
object(Ref\WeakReference)#5 (2) refcount(3){
  ["referent":"Ref\AbstractReference":private]=>
  object(stdClass)#2 (0) refcount(2){
  }
  ["notifier":"Ref\AbstractReference":private]=>
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

Notified: object(Ref\WeakReference)#5 (2) refcount(7){
  ["referent":"Ref\AbstractReference":private]=>
  NULL
  ["notifier":"Ref\AbstractReference":private]=>
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
Notified: object(Ref\WeakReference)#4 (2) refcount(7){
  ["referent":"Ref\AbstractReference":private]=>
  NULL
  ["notifier":"Ref\AbstractReference":private]=>
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
