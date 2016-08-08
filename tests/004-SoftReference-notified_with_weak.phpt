--TEST--
Ref\SoftReference - soft reference notifier then original object dtor and then soft notifiers called
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

require '.stubs.php';

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';


$obj = new \WeakTests\TrackingDtor();

$sr = new Ref\SoftReference($obj, function (Ref\SoftReference $reference) {
    echo 'Soft notifier called', PHP_EOL;
});

$wr = new Ref\WeakReference($obj, function (Ref\WeakReference $reference){
    echo 'Weak notifier called', PHP_EOL;
});

$obj = null;

?>
EOF
--EXPECT--
Soft notifier called
WeakTests\TrackingDtor's destructor called
Weak notifier called
EOF
