--TEST--
Ref\WeakReference - serialize extended reference that tries to implemet Serializable interface is not allowed
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php
/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new \stdClass();



class SerializableWeakReference extends \Ref\WeakReference implements Serializable {
    public function serialize()
    {
        // TODO: Implement serialize() method.
    }

    public function unserialize($serialized)
    {
        // TODO: Implement unserialize() method.
    }

}

$obj = new stdClass();

$s = new SerializableWeakReference($obj);

?>
EOF
--EXPECT--
Fatal error: Class SerializableWeakReference could not implement interface Serializable in Unknown on line 0
