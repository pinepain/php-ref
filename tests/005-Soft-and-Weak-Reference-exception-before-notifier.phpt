--TEST--
Ref\SoftReference + Ref\WeakReference - exception thrown outside notifier before destructing
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

use Ref\SoftReference;
use Ref\WeakReference;

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

function test()
{
    $obj = new stdClass();

    $sr = new SoftReference($obj, function () {
        echo 'Soft notifier called', PHP_EOL;
    });

    $wr = new WeakReference($obj, function () {
        echo 'Weak notifier called', PHP_EOL;
    });

    throw new RuntimeException('Test exception');
}

try {
    test();
} catch (Throwable $e) {
    $helper->exception_export($e);
}

?>
EOF
--EXPECT--
Soft notifier called
Weak notifier called
RuntimeException: Test exception
EOF
