--TEST--
Weak\Reference - notifier not called after weak reference dies first
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
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

$callback = function (Weak\Reference $reference) {
    echo 'Weak notifier called', PHP_EOL;
};

$wr = new Weak\Reference($obj, $callback);

try {
    $obj = null;
} catch(\Weak\NotifierException $e) {
    $helper->weak_exception_export($e);
    $helper->line();
}

$helper->assert('Referent object dead', $wr->get() === null);
$helper->assert('Referent object invalid', $wr->valid(), false);
$helper->line();

?>
EOF
--EXPECT--
Weak notifier called
Weak\NotifierException: One or more exceptions thrown during notifiers calling
    Exception: Destructor throws exception

Referent object dead: ok
Referent object invalid: ok

EOF
