--TEST--
Ref\SoftReference - callable notifier passed as string
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
$sr = new Ref\SoftReference($obj, 'notifier');
$obj = null;

$helper->line();
?>
EOF
--EXPECT--
Notified

EOF
