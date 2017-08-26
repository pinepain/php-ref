--TEST--
Ref\WeakReference - change notifier on cloned object
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$callback_notifier_1 = function() {
    echo 'Callback notifier 1', PHP_EOL;
};

$callback_notifier_2 = function() {
    echo 'Callback notifier 2', PHP_EOL;
};

$obj = new stdClass();
$sr = new Ref\SoftReference($obj, $callback_notifier_1);

$helper->assert('Notifier is callback', $sr->notifier(), $callback_notifier_1);

$sr1 = clone $sr;
$helper->assert('Cloned notifier is array', $sr1->notifier(), $callback_notifier_1);
$sr1->notifier($callback_notifier_2);
$helper->assert('Cloned notifier changed to it own callback', $sr1->notifier(), $callback_notifier_2);

$helper->line();
$obj = null;
$helper->line();
?>
EOF
--EXPECT--
Notifier is callback: ok
Cloned notifier is array: ok
Cloned notifier changed to it own callback: ok

Callback notifier 2
Callback notifier 1

EOF
