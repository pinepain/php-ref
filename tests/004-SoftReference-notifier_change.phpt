--TEST--
Weak\SoftReference - changing notifier
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
$sr = new Weak\SoftReference($obj);

$helper->assert('Notifier is null by default', $sr->notifier(), null);
$helper->assert('Notifier was null', $sr->notifier($array_notifier), null);
$helper->assert('Notifier is array', $sr->notifier(), $array_notifier);

$obj = null;
$helper->assert('New array notifier notified on referent object death', $array_notifier, [$sr]);
$helper->line();


$obj = new stdClass();
$array_notifier = [];
$sr = new Weak\SoftReference($obj, $array_notifier);

$helper->assert('Notifier is array by default', $sr->notifier(), $array_notifier);
$helper->assert('Notifier was array', $sr->notifier($callback_notifier), $array_notifier);
$helper->assert('Notifier is callback', $sr->notifier(), $callback_notifier);

$obj = null;
$helper->line();

$obj = new stdClass();
$array_notifier = [];
$sr = new Weak\SoftReference($obj, $callback_notifier);

$helper->assert('Notifier is callback by default', $sr->notifier(), $callback_notifier);
$helper->assert('Notifier was callback', $sr->notifier(null), $callback_notifier);
$helper->assert('Notifier is null', $sr->notifier(), null);
$obj = null;
$helper->line();

$notifier = 'var_dump';
$sr->notifier($notifier);

try {
    $sr->notifier('nonexistent');
} catch (Error $e) {
    $helper->exception_export($e);
}

$helper->assert('Notifier stays the same', $sr->notifier(), $notifier);
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

TypeError: Argument 2 passed to Weak\SoftReference::notifier() must be callable, array or null, string given
Notifier stays the same: ok

EOF
