--TEST--
Ref\SoftReference - multiple soft references to the same object
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

require '.stubs.php';


$obj = new \WeakTests\TrackingDtor();

$sr1 = new Ref\SoftReference($obj);
$sr2 = new Ref\SoftReference($obj);


$helper->assert("First soft references points to original object", $sr1->get() === $obj);
$helper->assert("Second soft references points to original object", $sr2->get() === $obj);

$helper->line();
$obj = null;
$helper->line();

$helper->assert("First soft references points to null", $sr1->get() === null);
$helper->assert("Second soft references points to null", $sr2->get() === null);

$sr1 = null;
$sr2 = null;

?>
EOF
--EXPECT--
First soft references points to original object: ok
Second soft references points to original object: ok

WeakTests\TrackingDtor's destructor called

First soft references points to null: ok
Second soft references points to null: ok
EOF
