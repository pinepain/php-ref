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

$callback = function (Weak\Reference $reference) {
    throw new \Exception('Test exception from callback');
};


$wr = new Weak\Reference($obj, $callback);

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
Exception: Test exception from callback
EOF
