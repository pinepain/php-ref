--TEST--
Ref\WeakReference - exception thrown outside notifier before destructing and in notifier
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

use Ref\NotifierException;
use Ref\WeakReference;

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

function test()
{
    $obj = new stdClass();

    $wr = new WeakReference($obj, function () {
        throw new RuntimeException('From weak notifier');
    });

    throw new RuntimeException('Test exception');
}

try {
    test();
} catch (NotifierException $e) {
    $helper->ref_exception_export($e);
}

?>
EOF
--EXPECT--
Ref\NotifierException: One or more exceptions thrown during notifiers calling
previous: RuntimeException: Test exception
thrown:
    #0: RuntimeException: From weak notifier
EOF
