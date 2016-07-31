--TEST--
Weak\SoftReference - soft reference notifier then original object dtor and then weak notifiers called
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

require '.stubs.php';

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';


$obj = new \WeakTests\TrackingDtor();

$sr = new Weak\SoftReference($obj, function (Weak\SoftReference $reference) {
    echo 'Soft notifier called', PHP_EOL;
});

$wr = new Weak\Reference($obj, function (Weak\Reference $reference){
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
