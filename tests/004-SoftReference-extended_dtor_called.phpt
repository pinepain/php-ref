--TEST--
Ref\SoftReference - dump representation of extended reference class
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new stdClass();

class ExtendedWeakReferenceTrackingDtor extends \Ref\SoftReference
{
    public function __destruct()
    {
        echo 'Dtoring ', get_class($this), PHP_EOL;
    }

}

$sr = new ExtendedWeakReferenceTrackingDtor($obj);

$sr = null;

$helper->line();
?>
EOF
--EXPECT--
Dtoring ExtendedWeakReferenceTrackingDtor

EOF
