--TEST--
Ref\SoftReference - exception thrown in callback
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

require '.stubs.php';

$obj = new \WeakTests\TrackingDtor();

$callback = function (Ref\SoftReference $reference) {
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
WeakTests\TrackingDtor's destructor called
Ref\NotifierException: One or more exceptions thrown during notifiers calling
    Exception: Test exception from callback
EOF
