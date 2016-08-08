--TEST--
Ref\WeakReference - destructor calls die()
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';


class TestExceptionInDestructor {
    public function __destruct()
    {
        echo 'Destructor dies', PHP_EOL;
        die();
    }
}

$obj = new TestExceptionInDestructor();

$callback = function (Ref\WeakReference $reference) {
    echo 'Weak notifier called', PHP_EOL;
};

$wr = new Ref\WeakReference($obj, $callback);

try {
    $obj = null;
} catch(Exception $e) {
    $helper->exception_export($e);
    $helper->line();
}

register_shutdown_function(function () use (&$wr, &$helper) {
    echo 'We did not die properly', PHP_EOL;
});


?>
EOF
--EXPECT--
Destructor dies
