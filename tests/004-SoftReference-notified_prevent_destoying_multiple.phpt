--TEST--
Ref\SoftReference - do not call later soft notifiers when object was prevented from being destroyed
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

use function \Ref\{
    is_obj_destructor_called
};

require '.stubs.php';

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';


$obj = new \WeakTests\TrackingDtor();
$obj_copy = true;

$sr1 = new Ref\SoftReference($obj, function () {
    echo 'Soft notifier called', PHP_EOL;
});

$sr2 = new Ref\SoftReference($obj, function (Ref\SoftReference $reference) use (&$obj, &$obj_copy) {
    echo 'Backup soft notifier called', PHP_EOL;

    if (true === $obj_copy) {
        echo 'Original object was prevented from being destroyed', PHP_EOL;
        $obj_copy = $reference->get();
    }
});


$obj = null;
$obj_copy = null;

?>
EOF
--EXPECT--
Backup soft notifier called
Original object was prevented from being destroyed
Backup soft notifier called
Soft notifier called
WeakTests\TrackingDtor's destructor called
EOF
