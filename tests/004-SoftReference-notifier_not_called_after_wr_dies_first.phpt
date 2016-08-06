--TEST--
Ref\SoftReference - notifier not called after weak reference dies first
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

class TestExceptionInDestructor {
    public function __destruct()
    {
        throw new Exception('Destructor throws exception');
    }
}

$obj = new TestExceptionInDestructor();

$callback = function (Ref\SoftReference $reference) {
    echo 'Weak notifier called', PHP_EOL;
};

$sr = new Ref\SoftReference($obj, $callback);

try {
    $obj = null;
} catch(\Ref\NotifierException $e) {
    $helper->ref_exception_export($e);
    $helper->line();
}

$helper->assert('Referent object dead', $sr->get() === null);
$helper->assert('Referent object invalid', $sr->valid(), false);
$helper->line();

?>
EOF
--EXPECT--
Weak notifier called
Ref\NotifierException: One or more exceptions thrown during notifiers calling
    Exception: Destructor throws exception

Referent object dead: ok
Referent object invalid: ok

EOF
