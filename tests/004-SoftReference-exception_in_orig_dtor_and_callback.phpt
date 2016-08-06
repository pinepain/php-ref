--TEST--
Weak\SoftReference - exception thrown in orig dtor and callback
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
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

$callback = function (Weak\SoftReference $reference) {
    echo 'Callback called', PHP_EOL;

    throw new \Exception('Test exception from callback');
};


$sr = new Weak\SoftReference($obj, $callback);


try {
    $obj = null;
} catch(\Weak\NotifierException $e) {
    $helper->weak_exception_export($e);
}

?>
EOF
--EXPECT--
Callback called
Dtor called
Weak\NotifierException: One or more exceptions thrown during notifiers calling
    Exception: Test exception from callback
    Exception: Test exception from dtor
EOF
