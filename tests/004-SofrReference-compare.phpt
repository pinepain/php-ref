--TEST--
Ref\SoftReference - compare references
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';


$obj1 = new stdClass();
$obj2 = new stdClass();
$obj3 = new SplObjectStorage();

$sr1 = $sr1a = new Ref\SoftReference($obj1);
$sr1b = new Ref\SoftReference($obj1);
$sr2 = new Ref\SoftReference($obj2);
$sr3 = new Ref\SoftReference($obj3);


$helper->assert('References to the same object matches', $sr1a == $sr1b);
$helper->assert('References to the same object do not match strictly', $sr1a !== $sr1b);
$helper->line();

$helper->assert('Different object same of a kind match', $obj1 == $obj2);
$helper->assert('References to the different object same of a kind match', $sr1 == $sr2);
// no compare_object handlers called during strict comparison
$helper->assert('Different object same of a kind do not match strictly', $obj1 !== $obj2);
$helper->assert('References to the different object same of a kind do not match strictly', $sr1 !== $sr2);
$helper->line();

$helper->assert('Different object different of a kind do not match', $obj1 != $obj3);
$helper->assert('References to the different object of the different kind do not match', $sr1 != $sr3);
// no compare_object handlers called during strict comparison
$helper->assert('Different object different of a kind do not match strictly', $obj1 !== $obj3);
$helper->assert('References to the different object of the different kind do not match strictly', $sr1a !== $sr3);
$helper->line();

$obj1 = null;

$helper->assert('References to the same object after their death matches', $sr1a == $sr1b);
$helper->assert('References to the same object after their death do not match strictly', $sr1a !== $sr1b);
$helper->line();

$helper->assert('References to the different object same of a kind after one\' death do not match', $sr1 != $sr2);
$helper->assert('References to the different object same of a kind after one\' death do not match strictly', $sr1 !== $sr2);
$helper->line();

$helper->assert('References to the different object of the different kind after one\' death do not match', $sr1 != $sr3);
$helper->assert('References to the different object of the different kind after one\' death do not match strictly', $sr1a !== $sr3);
$helper->line();


$obj1 = new stdClass();

$sr1 = new Ref\SoftReference($obj1, ['first']);
$sr2 = new Ref\SoftReference($obj1, ['second']);

$helper->assert('References to the same object with different notifiers do not match', $sr1 != $sr2);

$sr1 = new Ref\SoftReference($obj1, []);
$sr2 = new Ref\SoftReference($obj1, []);

$helper->assert('References to the same object with same notifiers match', $sr1 == $sr2);

$sr2->property = 'changed';

$helper->assert('References to the same object with same notifiers but different properties do not match', $sr1 != $sr2);

$sr3 = new class($obj1, []) extends Ref\SoftReference {};

$helper->assert('References to the same object with same notifiers do not match if they are instances of different classes', $sr2 != $sr3);

$helper->line();

?>
EOF
--EXPECT--
References to the same object matches: ok
References to the same object do not match strictly: ok

Different object same of a kind match: ok
References to the different object same of a kind match: ok
Different object same of a kind do not match strictly: ok
References to the different object same of a kind do not match strictly: ok

Different object different of a kind do not match: ok
References to the different object of the different kind do not match: ok
Different object different of a kind do not match strictly: ok
References to the different object of the different kind do not match strictly: ok

References to the same object after their death matches: ok
References to the same object after their death do not match strictly: ok

References to the different object same of a kind after one' death do not match: ok
References to the different object same of a kind after one' death do not match strictly: ok

References to the different object of the different kind after one' death do not match: ok
References to the different object of the different kind after one' death do not match strictly: ok

References to the same object with different notifiers do not match: ok
References to the same object with same notifiers match: ok
References to the same object with same notifiers but different properties do not match: ok
References to the same object with same notifiers do not match if they are instances of different classes: ok

EOF
