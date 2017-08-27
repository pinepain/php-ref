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
$wr = new Ref\WeakReference($obj, $callback_notifier_1);

$helper->assert('Notifier is callback', $wr->notifier(), $callback_notifier_1);

$wr1 = clone $wr;
$helper->assert('Cloned notifier is array', $wr1->notifier(), $callback_notifier_1);
$wr1->notifier($callback_notifier_2);
$helper->assert('Cloned notifier changed to it own callback', $wr1->notifier(), $callback_notifier_2);

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
