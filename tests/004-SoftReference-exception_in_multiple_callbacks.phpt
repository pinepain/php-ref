--TEST--
Ref\SoftReference - exception thrown in callback
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
    return function (Ref\SoftReference $reference) use ($id) {
        echo 'Callback #' . $id, ' called', PHP_EOL;
        throw new \Exception('Test exception from callback #' . $id);
    };
}

function callback_ok($id)
{
    return function (Ref\SoftReference $reference) use ($id) {
        echo 'Callback #' . $id, ' called', PHP_EOL;
    };
}


$sr5 = new Ref\SoftReference($obj, callback_ok(5));
$sr4 = new Ref\SoftReference($obj, callback_ok(4));
$sr3 = new Ref\SoftReference($obj, callback_throws(3));
$sr2 = new Ref\SoftReference($obj, callback_ok(2));
$sr1 = new Ref\SoftReference($obj, callback_throws(1));
$sr0 = new Ref\SoftReference($obj, callback_ok(0));

try {
    $obj = null;
} catch(\Ref\NotifierException $e) {
    $helper->ref_exception_export($e);
}


$helper->line();
?>
EOF
--EXPECT--
Callback #0 called
Callback #1 called
Callback #2 called
Callback #3 called
Callback #4 called
Callback #5 called
WeakTests\TrackingDtor's destructor called
Ref\NotifierException: One or more exceptions thrown during notifiers calling
    Exception: Test exception from callback #1
    Exception: Test exception from callback #3

EOF
