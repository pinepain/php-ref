--TEST--
Weak\SoftReference - callable notifier passed as string
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';


function notifier()
{
    echo 'Notified', PHP_EOL;
}

$obj = new stdClass();
$sr = new Weak\SoftReference($obj, 'notifier');
$obj = null;

$helper->line();
?>
EOF
--EXPECT--
Notified

EOF
