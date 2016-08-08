--TEST--
Ref\SoftReference - original object destructor called with notify callback after it
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

require '.stubs.php';

$callback = function (Ref\SoftReference $reference) {
    echo 'Weak notifier called', PHP_EOL;
};

$obj = new \WeakTests\TrackingDtor();

$sr = new Ref\SoftReference($obj, $callback);


$helper->assert("Weak references points to original object", $sr->get() === $obj);

$helper->line();
$obj = null;
$helper->line();

$helper->assert("Weak references points to null", $sr->get() === null);

$obj = null;

?>
EOF
--EXPECT--
Weak references points to original object: ok

Weak notifier called
WeakTests\TrackingDtor's destructor called

Weak references points to null: ok
EOF
