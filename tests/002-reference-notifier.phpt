--TEST--
Weak\Reference - notifier accessor
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new stdClass();

$array_notifier = [];
$callback_notifier = [$helper, 'dump'];

$wr0 = new Weak\Reference($obj);
$wr1 = new Weak\Reference($obj, null);
$wr2 = new Weak\Reference($obj, $array_notifier);
$wr3 = new Weak\Reference($obj, $callback_notifier);

$helper->assert('Notifier is null by default', $wr0->notifier(), null);
$helper->assert('Null notifier acceptable', $wr1->notifier(), null);
$helper->assert('Array notifier acceptable', $wr2->notifier(), $array_notifier);
$helper->assert('Callback notifier acceptable', $wr3->notifier(), $callback_notifier);

$helper->line();
?>
EOF
--EXPECT--
Notifier is null by default: ok
Null notifier acceptable: ok
Array notifier acceptable: ok
Callback notifier acceptable: ok

EOF
