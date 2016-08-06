--TEST--
Ref\SoftReference - SplObjectStorage hashes in debug output still consistent before and after wrapping
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip";  ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$s = new SplObjectStorage();
$obj = new stdClass();
$original_hash = spl_object_hash($obj);
$s->attach($obj);
$sr = new Ref\SoftReference($obj);
$current_hash = spl_object_hash($obj);
$helper->assert('Stored in SplObjectStorage soft-referenced object hash matches origin one', $original_hash == $current_hash);

ob_start();
debug_zval_dump($s);
$res = ob_get_contents();
ob_end_clean();

$helper->assert('Object hash in SplObjectStorage debug output not changed', false !== strpos($res, $original_hash));
$helper->line();

debug_zval_dump($original_hash);
debug_zval_dump($current_hash);
echo $res;

$helper->line();

?>
EOF
--EXPECTF--
Stored in SplObjectStorage soft-referenced object hash matches origin one: ok
Object hash in SplObjectStorage debug output not changed: ok

string(32) "%s" refcount(2)
string(32) "%s" refcount(2)
object(SplObjectStorage)#2 (1) refcount(2){
  ["storage":"SplObjectStorage":private]=>
  array(1) refcount(1){
    ["%s"]=>
    array(2) refcount(1){
      ["obj"]=>
      object(stdClass)#3 (0) refcount(2){
      }
      ["inf"]=>
      NULL
    }
  }
}

EOF
