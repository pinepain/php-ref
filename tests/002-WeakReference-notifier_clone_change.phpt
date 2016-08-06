--TEST--
Ref\WeakReference - change notifier on cloned object
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$callback_notifier = [$helper, 'dump'];

$obj = new stdClass();
$array_notifier = [];
$wr = new Ref\WeakReference($obj, $array_notifier);

$helper->assert('Notifier is array', $wr->notifier(), $array_notifier);

$array_notifier1 = [];

$wr1 = clone $wr;
$helper->assert('Cloned notifier is array', $wr1->notifier(), $array_notifier);
$wr1->notifier($array_notifier1);
$helper->assert('Cloned notifier changed to it own array', $wr1->notifier(), $array_notifier1);

$obj = null;
$helper->line();

$helper->assert('First array notifier notified', $array_notifier, [$wr]);
$helper->assert('Second array notifier notified', $array_notifier1, [$wr1]);
$helper->line();
?>
EOF
--EXPECT--
Notifier is array: ok
Cloned notifier is array: ok
Cloned notifier changed to it own array: ok

First array notifier notified: ok
Second array notifier notified: ok

EOF
