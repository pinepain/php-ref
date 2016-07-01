--TEST--
Weak\Reference - exception thrown in orig dtor and callback
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

$callback = function (Weak\Reference $reference) {
    echo 'Callback called', PHP_EOL;

    throw new \Exception('Test exception from callback');
};


$wr = new Weak\Reference($obj, $callback);


try {
    $obj = null;
} catch(\Weak\NotifierException $e) {
    $helper->weak_exception_export($e);
}

?>
EOF
--EXPECT--
Dtor called
Callback called
Weak\NotifierException: One or more exceptions thrown during notifiers calling
    Exception: Test exception from dtor
    Exception: Test exception from callback
EOF
