--TEST--
Ref\WeakReference - notifier accessor
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new stdClass();

$callback_notifier = [$helper, 'dump'];

$wr0 = new Ref\WeakReference($obj);
$wr1 = new Ref\WeakReference($obj, null);
$wr2 = new Ref\WeakReference($obj, $callback_notifier);

$helper->assert('Notifier is null by default', $wr0->notifier(), null);
$helper->assert('Null notifier acceptable', $wr1->notifier(), null);
$helper->assert('Callback notifier acceptable', $wr2->notifier(), $callback_notifier);

$helper->line();
?>
EOF
--EXPECT--
Notifier is null by default: ok
Null notifier acceptable: ok
Callback notifier acceptable: ok

EOF
