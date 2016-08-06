--TEST--
Weak\SoftReference - track only single specific object
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

require '.stubs.php';

$obj0 = new \WeakTests\TrackingDtor();
$obj1 = new \WeakTests\TrackingDtor();

$callback = function (Weak\SoftReference $reference) {
    echo 'Weak notifier called', PHP_EOL;
};


$sr0 = new Weak\SoftReference($obj0, $callback);

$obj1 = null;

$helper->line();
$obj0 = null;


?>
EOF
--EXPECT--
WeakTests\TrackingDtor's destructor called

Weak notifier called
WeakTests\TrackingDtor's destructor called
EOF
