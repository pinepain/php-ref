--TEST--
Weak\Reference - original object destructor called with notify callback before it
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

require '.stubs.php';

$callback = function (Weak\Reference $reference) {
    echo 'Weak notifier called', PHP_EOL;
};

$obj = new \WeakTests\TrackingDtor();

$wr = new Weak\Reference($obj, $callback);


$helper->assert("Weak references points to original object", $wr->get() === $obj);

$helper->line();
$obj = null;
$helper->line();

$helper->assert("Weak references points to null", $wr->get() === null);

$obj = null;

?>
EOF
--EXPECT--
Weak references points to original object: ok

WeakTests\TrackingDtor's destructor called
Weak notifier called

Weak references points to null: ok
EOF
