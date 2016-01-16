--TEST--
Weak\Reference - callable notifier passed as array
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';


class Test
{
    public $wr;

    public function __construct($obj)
    {
        $this->wr = new Weak\Reference($obj, [$this, 'notifier']);
    }

    public function notifier()
    {
        echo 'Notified', PHP_EOL;
    }
}

$obj = new stdClass();
$t = new Test($obj);
$obj = null;

$helper->line();
?>
EOF
--EXPECT--
Notified

EOF
