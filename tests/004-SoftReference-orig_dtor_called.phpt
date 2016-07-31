--TEST--
Weak\SoftReference - original object destructor called but notifier not when weak reference dies first
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

require '.stubs.php';

$obj = new \WeakTests\TrackingDtor();
$callback = function (Weak\SoftReference $reference) {
    echo 'Weak notifier called', PHP_EOL;
};

$sr = new Weak\SoftReference($obj, $callback);

$sr = null;
$obj = null;

?>
EOF
--EXPECT--
WeakTests\TrackingDtor's destructor called
EOF
