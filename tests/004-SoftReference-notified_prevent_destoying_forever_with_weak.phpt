--TEST--
Weak\SoftReference - prevent original object from being destroyed, weak notifiers called only when soft ref released
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

require '.stubs.php';

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';


$obj = new \WeakTests\TrackingDtor();
$obj_copy = null;

$sr = new Weak\SoftReference($obj, function (Weak\SoftReference $reference) use (&$obj, &$obj_copy) {
    echo 'Soft notifier called', PHP_EOL;

    $obj_copy = $reference->get();
});

$wr = new Weak\Reference($obj, function (Weak\Reference $reference){
    echo 'Weak notifier called', PHP_EOL;
});

$obj = null;
$sr = null;

echo 'Here original dtor will be called', PHP_EOL;
$obj_copy = null;

?>
EOF
--EXPECT--
Soft notifier called
Here original dtor will be called
WeakTests\TrackingDtor's destructor called
Weak notifier called
EOF
