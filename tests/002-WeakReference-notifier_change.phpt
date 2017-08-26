--TEST--
Ref\WeakReference - changing notifier
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
$wr = new Ref\WeakReference($obj);

$helper->assert('Notifier is null by default', $wr->notifier(), null);
$helper->assert('Notifier was null', $wr->notifier($callback_notifier_1), NULL);
$helper->assert('Notifier is callback', $wr->notifier(), $callback_notifier_1);

$obj = null;
$helper->line();


$obj = new stdClass();
$wr = new Ref\WeakReference($obj, $callback_notifier_1);

$helper->assert('Notifier is callback by default', $wr->notifier(), $callback_notifier_1);
$helper->assert('Notifier was callback', $wr->notifier($callback_notifier_2), $callback_notifier_1);
$helper->assert('Notifier is callback', $wr->notifier(), $callback_notifier_2);

$obj = null;
$helper->line();

$obj = new stdClass();
$wr = new Ref\WeakReference($obj, $callback_notifier_1);

$helper->assert('Notifier is callback by default', $wr->notifier(), $callback_notifier_1);
$helper->assert('Notifier was callback', $wr->notifier($callback_notifier_2), $callback_notifier_1);
$helper->assert('Notifier is callback', $wr->notifier(), $callback_notifier_2);

$obj = null;
$helper->line();


$notifier = 'var_dump';
$wr->notifier($notifier);

try {
    $wr->notifier('nonexistent');
} catch (Error $e) {
    $helper->exception_export($e);
}

$helper->assert('Notifier stays the same', $wr->notifier(), $notifier);
$helper->line();

?>
EOF
--EXPECT--
Notifier is null by default: ok
Notifier was null: ok
Notifier is callback: ok
Callback notifier 1

Notifier is callback by default: ok
Notifier was callback: ok
Notifier is callback: ok
Callback notifier 2

Notifier is callback by default: ok
Notifier was callback: ok
Notifier is callback: ok
Callback notifier 2

TypeError: Argument 1 passed to Ref\AbstractReference::notifier() must be callable or null, string given
Notifier stays the same: ok

EOF
