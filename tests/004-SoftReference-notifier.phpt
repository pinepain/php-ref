--TEST--
Weak\SoftReference - notifier accessor
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new stdClass();

$array_notifier = [];
$callback_notifier = [$helper, 'dump'];

$sr0 = new Weak\SoftReference($obj);
$sr1 = new Weak\SoftReference($obj, null);
$sr2 = new Weak\SoftReference($obj, $array_notifier);
$sr3 = new Weak\SoftReference($obj, $callback_notifier);

$helper->assert('Notifier is null by default', $sr0->notifier(), null);
$helper->assert('Null notifier acceptable', $sr1->notifier(), null);
$helper->assert('Array notifier acceptable', $sr2->notifier(), $array_notifier);
$helper->assert('Callback notifier acceptable', $sr3->notifier(), $callback_notifier);

$helper->line();
?>
EOF
--EXPECT--
Notifier is null by default: ok
Null notifier acceptable: ok
Array notifier acceptable: ok
Callback notifier acceptable: ok

EOF
