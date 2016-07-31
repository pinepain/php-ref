--TEST--
Weak\SoftReference - change notifier on cloned object
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$callback_notifier = [$helper, 'dump'];

$obj = new stdClass();
$array_notifier = [];
$sr = new Weak\SoftReference($obj, $array_notifier);

$helper->assert('Notifier is array', $sr->notifier(), $array_notifier);

$array_notifier1 = [];

$sr1 = clone $sr;
$helper->assert('Cloned notifier is array', $sr1->notifier(), $array_notifier);
$sr1->notifier($array_notifier1);
$helper->assert('Cloned notifier changed to it own array', $sr1->notifier(), $array_notifier1);

$obj = null;
$helper->line();

$helper->assert('First array notifier notified', $array_notifier, [$sr]);
$helper->assert('Second array notifier notified', $array_notifier1, [$sr1]);
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
