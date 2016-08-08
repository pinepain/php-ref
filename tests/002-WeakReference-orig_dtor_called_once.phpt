--TEST--
Ref\WeakReference - multiple weak references to the same object, original object destructor called once
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

require '.stubs.php';

$obj = new \WeakTests\TrackingDtor();

$wr1 = new Ref\WeakReference($obj);
$wr2 = new Ref\WeakReference($obj);


$obj = null;

?>
EOF
--EXPECT--
WeakTests\TrackingDtor's destructor called
EOF
