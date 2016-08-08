--TEST--
Ref\SoftReference - clone reference
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

use function \Ref\{
    softrefcount,
    softrefs
};

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new \stdClass();

$notifier = function (Ref\SoftReference $ref) use ($helper) {
    echo 'Notified: ';
    $helper->dump($ref);
};

$ref = new \Ref\SoftReference($obj, $notifier);

$helper->export_annotated('softrefcount($obj)', softrefcount($obj));
$helper->dump($ref);
$helper->line();

$ref2 = clone $ref;

$helper->assert('Cloned soft reference matches original', $ref == $ref2);
$helper->assert('Cloned soft reference does not match original soft reference strictly', $ref !== $ref2);
$helper->line();

$helper->export_annotated('softrefcount($obj)', softrefcount($obj));
$helper->dump($ref2);
$helper->line();

$helper->assert('Soft references reported with cloned reference', softrefs($obj), [$ref, $ref2]);
$helper->line();

$obj = null;
$helper->line();

$helper->assert('Cloned soft reference matches original', $ref == $ref2);
$helper->assert('Cloned soft reference does not match original soft reference strictly', $ref !== $ref2);
$helper->line();


?>
EOF
--EXPECT--
softrefcount($obj): integer: 1
object(Ref\SoftReference)#4 (2) refcount(3){
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

Cloned soft reference matches original: ok
Cloned soft reference does not match original soft reference strictly: ok

softrefcount($obj): integer: 2
object(Ref\SoftReference)#5 (2) refcount(3){
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

Soft references reported with cloned reference: ok

Notified: object(Ref\SoftReference)#5 (2) refcount(6){
  ["referent":"Ref\AbstractReference":private]=>
  object(stdClass)#2 (0) refcount(2){
  }
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
Notified: object(Ref\SoftReference)#4 (2) refcount(6){
  ["referent":"Ref\AbstractReference":private]=>
  object(stdClass)#2 (0) refcount(2){
  }
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

Cloned soft reference matches original: ok
Cloned soft reference does not match original soft reference strictly: ok

EOF
