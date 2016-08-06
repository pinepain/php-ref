--TEST--
Ref\WeakReference - dump representation of extended reference class
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new stdClass();

class ExtendedWeakReferenceTrackingDtor extends \Ref\WeakReference
{
    public function __destruct()
    {
        echo 'Dtoring ', get_class($this), PHP_EOL;
    }

}

$wr = new ExtendedWeakReferenceTrackingDtor($obj);

$wr = null;

$helper->line();
?>
EOF
--EXPECT--
Dtoring ExtendedWeakReferenceTrackingDtor

EOF
