--TEST--
Weak\SoftReference - exception thrown in callback
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

require '.stubs.php';

$obj = new \WeakTests\TrackingDtor();

$callback = function (Weak\SoftReference $reference) {
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
WeakTests\TrackingDtor's destructor called
Weak\NotifierException: One or more exceptions thrown during notifiers calling
    Exception: Test exception from callback
EOF
