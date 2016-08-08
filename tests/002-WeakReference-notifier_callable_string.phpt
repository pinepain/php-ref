--TEST--
Ref\WeakReference - callable notifier passed as string
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';


function notifier()
{
    echo 'Notified', PHP_EOL;
}

$obj = new stdClass();
$wr = new Ref\WeakReference($obj, 'notifier');
$obj = null;

$helper->line();
?>
EOF
--EXPECT--
Notified

EOF
