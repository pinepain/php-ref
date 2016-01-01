--TEST--
Weak\Reference - multiple weak references to the same object, original object destructor called once
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


$obj = null;

?>
EOF
--EXPECT--
WeakTests\TrackingDtor's destructor called
EOF
