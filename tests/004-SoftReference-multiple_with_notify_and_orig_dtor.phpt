--TEST--
Weak\SoftReference - multiple weak references with notifiers, original object destructor called once and after all notifiers
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

require '.stubs.php';

$obj = new \WeakTests\TrackingDtor();

$callback1 = function (Weak\SoftReference $reference) {
    echo 'Weak notifier 1 called', PHP_EOL;
};

$callback2 = function (Weak\SoftReference $reference) {
    echo 'Weak notifier 2 called', PHP_EOL;
};

$sr1 = new Weak\SoftReference($obj, $callback1);
$sr2 = new Weak\SoftReference($obj, $callback2);

$obj = null;

?>
EOF
--EXPECT--
Weak notifier 2 called
Weak notifier 1 called
WeakTests\TrackingDtor's destructor called
EOF
