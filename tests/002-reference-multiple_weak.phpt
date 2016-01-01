--TEST--
Weak\Reference - multiple weak references to the same object
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

require '.stubs.php';


$obj = new \WeakTests\TrackingDtor();

$wr1 = new Weak\Reference($obj);
$wr2 = new Weak\Reference($obj);


$helper->assert("First weak references points to original object", $wr1->get() === $obj);
$helper->assert("Second weak references points to original object", $wr2->get() === $obj);

$helper->line();
$obj = null;
$helper->line();

$helper->assert("First weak references points to null", $wr1->get() === null);
$helper->assert("Second weak references points to null", $wr2->get() === null);

$wr1 = null;
$wr2 = null;

?>
EOF
--EXPECT--
First weak references points to original object: ok
Second weak references points to original object: ok

WeakTests\TrackingDtor's destructor called

First weak references points to null: ok
Second weak references points to null: ok
EOF
