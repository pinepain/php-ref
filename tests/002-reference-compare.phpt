--TEST--
Weak\Reference - compare references
--SKIPIF--
<?php if (!extension_loaded("weak")) print "skip"; ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';


$obj1 = new stdClass();
$obj2 = new stdClass();
$obj3 = new SplObjectStorage();

$wr1 = $wr1a = new Weak\Reference($obj1);
$wr1b = new Weak\Reference($obj1);
$wr2 = new Weak\Reference($obj2);
$wr3 = new Weak\Reference($obj3);


$helper->assert('References to the same object matches', $wr1a == $wr1b);
$helper->assert('References to the same object do not match strictly', $wr1a !== $wr1b);
$helper->line();

$helper->assert('Different object same of a kind match', $obj1 == $obj2);
$helper->assert('References to the different object same of a kind match', $wr1 == $wr2);
// no compare_object handlers called during strict comparison
$helper->assert('Different object same of a kind do not match strictly', $obj1 !== $obj2);
$helper->assert('References to the different object same of a kind do not match strictly', $wr1 !== $wr2);
$helper->line();

$helper->assert('Different object different of a kind do not match', $obj1 != $obj3);
$helper->assert('References to the different object of the different kind do not match', $wr1 != $wr3);
// no compare_object handlers called during strict comparison
$helper->assert('Different object different of a kind do not match strictly', $obj1 !== $obj3);
$helper->assert('References to the different object of the different kind do not match strictly', $wr1a !== $wr3);
$helper->line();

$obj1 = null;

$helper->assert('References to the same object after their death matches', $wr1a == $wr1b);
$helper->assert('References to the same object after their death do not match strictly', $wr1a !== $wr1b);
$helper->line();

$helper->assert('References to the different object same of a kind after one\' death do not match', $wr1 != $wr2);
$helper->assert('References to the different object same of a kind after one\' death do not match strictly', $wr1 !== $wr2);
$helper->line();

$helper->assert('References to the different object of the different kind after one\' death do not match', $wr1 != $wr3);
$helper->assert('References to the different object of the different kind after one\' death do not match strictly', $wr1a !== $wr3);
$helper->line();


$obj1 = new stdClass();

$wr1 = new Weak\Reference($obj1, ['first']);
$wr2 = new Weak\Reference($obj1, ['second']);

$helper->assert('References to the same object with different notifiers do not match', $wr1 != $wr2);

$wr1 = new Weak\Reference($obj1, []);
$wr2 = new Weak\Reference($obj1, []);

$helper->assert('References to the same object with same notifiers match', $wr1 == $wr2);

$wr2->property = 'changed';

$helper->assert('References to the same object with same notifiers but different properties do not match', $wr1 != $wr2);

$wr3 = new class($obj1, []) extends Weak\Reference {};

$helper->assert('References to the same object with same notifiers do not match if they are instances of different classes', $wr2 != $wr3);

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
