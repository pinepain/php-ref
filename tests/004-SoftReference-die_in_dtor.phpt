--TEST--
Weak\SoftReference - destructor calls die()
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
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

$callback = function (Weak\SoftReference $reference) {
    echo 'Soft notifier called', PHP_EOL;
};

$sr = new Weak\SoftReference($obj, $callback);

try {
    $obj = null;
} catch(Exception $e) {
    $helper->exception_export($e);
    $helper->line();
}

register_shutdown_function(function () use (&$sr, &$helper) {
    echo 'We did not die properly', PHP_EOL;
});


?>
EOF
--EXPECT--
Soft notifier called
Destructor dies
