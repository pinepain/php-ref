--TEST--
Weak\Reference - exception thrown in callback
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
    return function (Weak\Reference $reference) use ($id) {
        throw new \Exception('Test exception from callback #' . $id);
    };
}

function callback_ok($id)
{
    return function (Weak\Reference $reference) use ($id) {

        echo 'Callback #' . $id, ' called', PHP_EOL;
    };
}


$wr3 = new Weak\Reference($obj, callback_ok(3));
$wr2 = new Weak\Reference($obj, callback_throws(2));
$wr1 = new Weak\Reference($obj, callback_throws(1));
$wr0 = new Weak\Reference($obj, callback_ok(0));

try {
    $obj = null;
} catch(\Exception $e) {
    $helper->exception_export($e);

    if ($e->getPrevious()) {
        echo 'previous:';
        $helper->exception_export($e->getPrevious());
    }
}

?>
EOF
--EXPECT--
WeakTests\TrackingDtor's destructor called
Callback #0 called
Exception: Test exception from callback #1
EOF
