--TEST--
Weak\SoftReference - multiple weak references to the same object, original object destructor called once
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


$obj = null;

?>
EOF
--EXPECT--
WeakTests\TrackingDtor's destructor called
EOF
