--TEST--
Ref\SoftReference - original object destructor called but notifier not when soft reference dies first
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

require '.stubs.php';

$obj = new \WeakTests\TrackingDtor();
$callback = function (Ref\SoftReference $reference) {
    echo 'Weak notifier called', PHP_EOL;
};

$sr = new Ref\SoftReference($obj, $callback);

$sr = null;
$obj = null;

?>
EOF
--EXPECT--
WeakTests\TrackingDtor's destructor called
EOF
