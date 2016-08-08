--TEST--
Ref\WeakReference - original object destructor called but notifier not when weak reference dies first
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

require '.stubs.php';

$obj = new \WeakTests\TrackingDtor();
$callback = function (Ref\WeakReference $reference) {
    echo 'Weak notifier called', PHP_EOL;
};

$wr = new Ref\WeakReference($obj, $callback);

$wr = null;
$obj = null;

?>
EOF
--EXPECT--
WeakTests\TrackingDtor's destructor called
EOF
