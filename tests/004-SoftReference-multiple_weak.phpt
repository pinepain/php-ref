--TEST--
Weak\SoftReference - multiple weak references to the same object
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

require '.stubs.php';


$obj = new \WeakTests\TrackingDtor();

$sr1 = new Weak\SoftReference($obj);
$sr2 = new Weak\SoftReference($obj);


$helper->assert("First weak references points to original object", $sr1->get() === $obj);
$helper->assert("Second weak references points to original object", $sr2->get() === $obj);

$helper->line();
$obj = null;
$helper->line();

$helper->assert("First weak references points to null", $sr1->get() === null);
$helper->assert("Second weak references points to null", $sr2->get() === null);

$sr1 = null;
$sr2 = null;

?>
EOF
--EXPECT--
First weak references points to original object: ok
Second weak references points to original object: ok

WeakTests\TrackingDtor's destructor called

First weak references points to null: ok
Second weak references points to null: ok
EOF
