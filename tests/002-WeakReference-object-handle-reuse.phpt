--TEST--
Ref\WeakReference - reference should work for newly create tracked objects with same handles as previously tracked objects
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

use Ref\WeakReference;

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';


class Test {
    public $storage = [];

    public function put($key, $value, string $hash)
    {
        $key = new WeakReference($key, function () use ($hash) {
            unset($this->storage[$hash]);
        });

        $value = new WeakReference($value, function () use ($hash) {
            unset($this->storage[$hash]);
        });

        $this->storage[$hash] = [$key, $value];
    }
}


$map = new Test();

$key_1   = new stdClass();
$value_1 = new stdClass();

$key_2   = new stdClass();
$value_2 = new stdClass();

$map->put($key_1, $value_1, 'test_1');
$map->put($key_2, $value_2, 'test_2');

$helper->assert('count', 2, count($map->storage));


$key_1 = null;
$helper->assert('count', 1, count($map->storage));


$value_2 = null;
$helper->assert('count', 0, count($map->storage));

$key_1   = new stdClass();
$value_1 = new stdClass();

$key_2   = new stdClass();
$value_2 = new stdClass();

$map->put($key_1, $value_1, 'test_1');
$map->put($key_2, $value_2, 'test_2');

$helper->assert('count', 2, count($map->storage));

$key_1 = null;
$helper->assert('count', 1, count($map->storage));

$value_2 = null;
$helper->assert('count', 0, count($map->storage));


?>
--EXPECT--
count: ok
count: ok
count: ok
count: ok
count: ok
count: ok
