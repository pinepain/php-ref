--TEST--
Weak\SoftReference - SplObjectStorage::getHash() still consistent before and after wrapping
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip";  ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new stdClass();

$s = new SplObjectStorage();

$original_hash = $s->getHash($obj);

$sr = new Weak\SoftReference($obj);

$current_hash = $s->getHash($obj);

$helper->assert('Wrapped object hash matches origin one', $original_hash == $current_hash);


$sr2 = new Weak\SoftReference($obj);

$double_hash = $s->getHash($obj);
$helper->assert('Repeatedly wrapped object hash does not changes', $current_hash == $double_hash);

$sr = null;

$again_hash = $s->getHash($obj);
$helper->assert('Repeatedly wrapped object hash does not changes after some reference death', $current_hash == $again_hash);

$sr2 = null;

$nullified_hash = $s->getHash($obj);
$helper->assert('Wrapped object hash still not changed even after all references died', $current_hash == $original_hash);
$helper->assert('Wrapped object hash still the same even after all references died', $current_hash == $nullified_hash);

$helper->line();

$s = new SplObjectStorage();
$obj = new stdClass();
$original_hash = spl_object_hash($obj);
$s->attach($obj);
$current_hash = spl_object_hash($obj);
$helper->assert('Stored in SplObjectStorage object hash matches origin one', $original_hash == $current_hash);

$s = new SplObjectStorage();
$obj = new stdClass();
$original_hash = spl_object_hash($obj);
$sr = new Weak\SoftReference($obj);
$s->attach($sr);
$current_hash = spl_object_hash($obj);
$helper->assert('Stored in SplObjectStorage weak-referenced object hash matches origin one', $original_hash == $current_hash);

$helper->line();

?>
EOF
--EXPECT--
Wrapped object hash matches origin one: ok
Repeatedly wrapped object hash does not changes: ok
Repeatedly wrapped object hash does not changes after some reference death: ok
Wrapped object hash still not changed even after all references died: ok
Wrapped object hash still the same even after all references died: ok

Stored in SplObjectStorage object hash matches origin one: ok
Stored in SplObjectStorage weak-referenced object hash matches origin one: ok

EOF
