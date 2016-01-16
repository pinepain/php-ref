--TEST--
Weak\Reference - changing notifier
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$callback_notifier = function() {
    echo 'Callback notified', PHP_EOL;
};

$obj = new stdClass();
$array_notifier = [];
$wr = new Weak\Reference($obj);

$helper->assert('Notifier is null by default', $wr->notifier(), null);
$helper->assert('Notifier was null', $wr->notifier($array_notifier), null);
$helper->assert('Notifier is array', $wr->notifier(), $array_notifier);

$obj = null;
$helper->assert('New array notifier notified on referent object death', $array_notifier, [$wr]);
$helper->line();


$obj = new stdClass();
$array_notifier = [];
$wr = new Weak\Reference($obj, $array_notifier);

$helper->assert('Notifier is array by default', $wr->notifier(), $array_notifier);
$helper->assert('Notifier was array', $wr->notifier($callback_notifier), $array_notifier);
$helper->assert('Notifier is callback', $wr->notifier(), $callback_notifier);

$obj = null;
$helper->line();

$obj = new stdClass();
$array_notifier = [];
$wr = new Weak\Reference($obj, $callback_notifier);

$helper->assert('Notifier is callback by default', $wr->notifier(), $callback_notifier);
$helper->assert('Notifier was callback', $wr->notifier(null), $callback_notifier);
$helper->assert('Notifier is null', $wr->notifier(), null);
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
Notifier is array: ok
New array notifier notified on referent object death: ok

Notifier is array by default: ok
Notifier was array: ok
Notifier is callback: ok
Callback notified

Notifier is callback by default: ok
Notifier was callback: ok
Notifier is null: ok

TypeError: Argument 2 passed to Weak\Reference::notifier() must be callable, array or null, string given
Notifier stays the same: ok

EOF
