--TEST--
Weak\SoftReference - invalid notifier callback passed
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new stdClass();

try {
  $sr = new Weak\SoftReference($obj, 'nonexistent');
} catch (TypeError $e) {
  $helper->exception_export($e);
}

$helper->line();
?>
EOF
--EXPECT--
TypeError: Argument 2 passed to Weak\SoftReference::__construct() must be callable, array or null, string given

EOF
