--TEST--
Ref\SoftReference - exception thrown in orig dtor and callback
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

$callback = function (Ref\SoftReference $reference) {
    echo 'Callback called', PHP_EOL;

    throw new \Exception('Test exception from callback');
};


$sr = new Ref\SoftReference($obj, $callback);


try {
    $obj = null;
} catch(\Ref\NotifierException $e) {
    $helper->ref_exception_export($e);
}

?>
EOF
--EXPECT--
Callback called
Dtor called
Ref\NotifierException: One or more exceptions thrown during notifiers calling
thrown:
    #0: Exception: Test exception from callback
    #1: Exception: Test exception from dtor
EOF
