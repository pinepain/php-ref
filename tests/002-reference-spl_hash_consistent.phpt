--TEST--
Weak\Reference - spl_object_hash still consisten before and after wa
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip";  ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new stdClass();


$original_hash = spl_object_hash($obj);

$wr = new Weak\Reference($obj);

$current_hash = spl_object_hash($obj);

$helper->assert('Wrapped object hash matches origin one', $original_hash == $current_hash);


$wr2 = new Weak\Reference($obj);

$double_hash = spl_object_hash($obj);
$helper->assert('Repeatedly wrapped object hash does not changes', $current_hash == $double_hash);

$wr = null;

$again_hash = spl_object_hash($obj);
$helper->assert('Repeatedly wrapped object hash does not changes after some reference death', $current_hash == $again_hash);

$wr2 = null;

$nullified_hash = spl_object_hash($obj);
$helper->assert('Wrapped object hash still not changed even after all references died', $current_hash == $original_hash);
$helper->assert('Wrapped object hash still the same even after all references died', $current_hash == $nullified_hash);

$helper->line();
?>
EOF
--EXPECT--
Wrapped object hash matches origin one: ok
Repeatedly wrapped object hash does not changes: ok
Repeatedly wrapped object hash does not changes after some reference death: ok
Wrapped object hash still not changed even after all references died: ok
Wrapped object hash still the same even after all references died: ok

EOF
