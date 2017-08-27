--TEST--
Ref\SoftReference - notifier accessor
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new stdClass();

$callback_notifier = [$helper, 'dump'];

$sr0 = new Ref\SoftReference($obj);
$sr1 = new Ref\SoftReference($obj, null);
$sr2 = new Ref\SoftReference($obj, $callback_notifier);

$helper->assert('Notifier is null by default', $sr0->notifier(), null);
$helper->assert('Null notifier acceptable', $sr1->notifier(), null);
$helper->assert('Callback notifier acceptable', $sr2->notifier(), $callback_notifier);

$helper->line();
?>
EOF
--EXPECT--
Notifier is null by default: ok
Null notifier acceptable: ok
Callback notifier acceptable: ok

EOF
