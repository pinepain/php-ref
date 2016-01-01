--TEST--
Weak\Reference - exception thrown in orig dtor and callback
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

class BadDtor {
    function __destruct()
    {
        throw new Exception('Test exception from dtor');
    }
}

$obj = new BadDtor();

$callback = function (Weak\Reference $reference) {
    throw new \Exception('Test exception from callback');
};


$wr = new Weak\Reference($obj, $callback);


try {
    $obj = null;
} catch(\Exception $e) {
    $helper->exception_export($e);

    if ($e->getPrevious()) {
        echo 'previous:';
        $helper->exception_export($e->getPrevious());
    }
}

?>
EOF
--EXPECT--
Exception: Test exception from dtor
EOF
