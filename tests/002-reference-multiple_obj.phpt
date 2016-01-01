--TEST--
Weak\Reference - track only single specific object
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

require '.stubs.php';

$obj0 = new \WeakTests\TrackingDtor();
$obj1 = new \WeakTests\TrackingDtor();

$callback = function (Weak\Reference $reference) {
    echo 'Weak notifier called', PHP_EOL;
};


$wr0 = new Weak\Reference($obj0, $callback);

$obj1 = null;

$helper->line();
$obj0 = null;


?>
EOF
--EXPECT--
WeakTests\TrackingDtor's destructor called

WeakTests\TrackingDtor's destructor called
Weak notifier called
EOF
