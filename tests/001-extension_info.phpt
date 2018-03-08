--TEST--
ref extension info
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

$ext = new ReflectionExtension('ref');
$ext->info();

?>
--EXPECTF--
ref

Ref support => enabled
Version => %s
Revision => %s
Compiled => %s @ %s
