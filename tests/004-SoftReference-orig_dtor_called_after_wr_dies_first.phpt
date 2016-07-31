--TEST--
Weak\SoftReference - original object destructor called after weak reference dies first
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

require '.stubs.php';

$obj = new \WeakTests\TrackingDtor();

$sr = new Weak\SoftReference($obj);

$sr = null;
$obj = null;

?>
EOF
--EXPECT--
WeakTests\TrackingDtor's destructor called
EOF
