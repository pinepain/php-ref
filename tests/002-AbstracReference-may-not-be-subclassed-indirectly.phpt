--TEST--
Weak\AbstractReference - may not be subclassed indirectly
--SKIPIF--
<?php if (!extension_loaded("weak")) {
    print "skip";
} ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new stdClass();
try {
    $rc = new ReflectionClass(Weak\AbstractReference::class);
    $obj = $rc->newInstanceWithoutConstructor();

} catch (Throwable $e) {
    $helper->exception_export($e);
}


try {
    new class($obj) extends Weak\AbstractReference
    {
    };
} catch (Throwable $e) {
    $helper->exception_export($e);
}


?>
--EXPECT--
Error: Cannot instantiate abstract class Weak\AbstractReference
Error: Weak\AbstractReference class may not be subclassed directly
