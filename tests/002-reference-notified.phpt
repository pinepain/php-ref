--TEST--
Weak\Reference - weak reference notified when object destroyed
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new stdClass();

$wr = new Weak\Reference($obj, function ($reference = null) use ($helper, &$obj) {
    $helper->assert('Notifier called', true);
    $helper->assert('Notifier get 1 argument', sizeof(func_get_args()) === 1);
    $helper->assert('Notifier get weak reference as it argument', $reference instanceof Weak\Reference);
    $helper->assert('Weak reference in notifier points to null', null === $reference->get());
});

$obj = null;

?>
EOF
--EXPECT--
Notifier called: ok
Notifier get 1 argument: ok
Notifier get weak reference as it argument: ok
Weak reference in notifier points to null: ok
EOF
