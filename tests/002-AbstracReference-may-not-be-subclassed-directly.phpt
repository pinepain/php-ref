--TEST--
Ref\AbstractReference - may not be subclassed directly
--SKIPIF--
<?php if (!extension_loaded("ref")) {
    print "skip";
} ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new stdClass();
try {
    new Ref\AbstractReference($obj);
} catch (Throwable $e) {
    $helper->exception_export($e);
}


try {
    new class($obj) extends Ref\AbstractReference
    {
    };
} catch (Throwable $e) {
    $helper->exception_export($e);
}


?>
--EXPECT--
Error: Cannot instantiate abstract class Ref\AbstractReference
Error: Ref\AbstractReference class may not be subclassed directly
