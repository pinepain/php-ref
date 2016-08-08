--TEST--
Ref\WeakReference - exception thrown in orig dtor
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

class BadDtor {
    function __destruct()
    {
        echo 'Dtor called', PHP_EOL;

        throw new Exception('Test exception from dtor');
    }
}

$obj = new BadDtor();

$callback = function (Ref\WeakReference $reference) {
    echo 'Weak callback called', PHP_EOL;
};


$wr = new Ref\WeakReference($obj, $callback);

try {
    $obj = null;
} catch(\Ref\NotifierException $e) {
    $helper->ref_exception_export($e);
}

?>
EOF
--EXPECT--
Dtor called
Weak callback called
Ref\NotifierException: One or more exceptions thrown during notifiers calling
    Exception: Test exception from dtor
EOF
