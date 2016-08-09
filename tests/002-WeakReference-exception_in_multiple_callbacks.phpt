--TEST--
Ref\WeakReference - exception thrown in callback
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

require '.stubs.php';

$obj = new \WeakTests\TrackingDtor(0);

function callback_throws($id)
{
    return function (Ref\WeakReference $reference) use ($id) {
        echo 'Callback #' . $id, ' called', PHP_EOL;
        throw new \Exception('Test exception from callback #' . $id);
    };
}

function callback_ok($id)
{
    return function (Ref\WeakReference $reference) use ($id) {
        echo 'Callback #' . $id, ' called', PHP_EOL;
    };
}


$wr5 = new Ref\WeakReference($obj, callback_ok(5));
$wr4 = new Ref\WeakReference($obj, callback_ok(4));
$wr3 = new Ref\WeakReference($obj, callback_throws(3));
$wr2 = new Ref\WeakReference($obj, callback_ok(2));
$wr1 = new Ref\WeakReference($obj, callback_throws(1));
$wr0 = new Ref\WeakReference($obj, callback_ok(0));

try {
    $obj = null;
} catch(\Ref\NotifierException $e) {
    $helper->ref_exception_export($e);
}


$helper->line();
?>
EOF
--EXPECT--
WeakTests\TrackingDtor's destructor called
Callback #0 called
Callback #1 called
Callback #2 called
Callback #3 called
Callback #4 called
Callback #5 called
Ref\NotifierException: One or more exceptions thrown during notifiers calling
thrown:
    #0: Exception: Test exception from callback #1
    #1: Exception: Test exception from callback #3

EOF
