--TEST--
Weak\SoftReference - exception thrown in callback
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

require '.stubs.php';

$obj = new \WeakTests\TrackingDtor(0);

function callback_throws($id)
{
    return function (Weak\SoftReference $reference) use ($id) {
        echo 'Callback #' . $id, ' called', PHP_EOL;
        throw new \Exception('Test exception from callback #' . $id);
    };
}

function callback_ok($id)
{
    return function (Weak\SoftReference $reference) use ($id) {
        echo 'Callback #' . $id, ' called', PHP_EOL;
    };
}


$sr5 = new Weak\SoftReference($obj, callback_ok(5));
$sr4 = new Weak\SoftReference($obj, callback_ok(4));
$sr3 = new Weak\SoftReference($obj, callback_throws(3));
$sr2 = new Weak\SoftReference($obj, callback_ok(2));
$sr1 = new Weak\SoftReference($obj, callback_throws(1));
$sr0 = new Weak\SoftReference($obj, callback_ok(0));

try {
    $obj = null;
} catch(\Weak\NotifierException $e) {
    $helper->weak_exception_export($e);
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
Weak\NotifierException: One or more exceptions thrown during notifiers calling
    Exception: Test exception from callback #1
    Exception: Test exception from callback #3

EOF
