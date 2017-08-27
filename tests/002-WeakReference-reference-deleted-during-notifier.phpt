--TEST--
Ref\WeakReference - reference deleted during notifier call
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

use Ref\WeakReference;

class Test
{
    public $storage = [];

    public function put($key, $value, string $hash)
    {
        $key = new WeakReference($key, function () use ($hash) {
            echo __METHOD__, PHP_EOL;
            var_dump($this);
            unset($this->storage[$hash]);
            var_dump($this);
        });

        $this->storage[$hash] = $key;
    }
}

$map = new Test();

$key_1   = new stdClass();
$value_1 = new stdClass();

$map->put($key_1, $value_1, 'test');

$key_1 = null;
?>
--EXPECT--
{closure}
object(Test)#1 (1) {
  ["storage"]=>
  array(1) {
    ["test"]=>
    object(Ref\WeakReference)#4 (2) {
      ["referent":"Ref\AbstractReference":private]=>
      NULL
      ["notifier":"Ref\AbstractReference":private]=>
      object(Closure)#5 (2) {
        ["static"]=>
        array(1) {
          ["hash"]=>
          string(4) "test"
        }
        ["this"]=>
        *RECURSION*
      }
    }
  }
}
object(Test)#1 (1) {
  ["storage"]=>
  array(0) {
  }
}
