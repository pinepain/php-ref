--TEST--
Check all extension entities
--SKIPIF--
<?php if (!extension_loaded("ref")) print "skip"; ?>
--FILE--
<?php

class Dumper
{
    public function dumpExtension()
    {
        $re = new ReflectionExtension('ref');

        // echo 'Name: ', $re->getName(), PHP_EOL;
        // echo 'Version: ', $re->getVersion(), PHP_EOL;

        echo PHP_EOL;
        echo 'Extension-global functions:', PHP_EOL;

        if ($re->getFunctions()) {
            foreach ($re->getFunctions() as $rf) {
                $this->dumpFunction($rf);
            }
        } else {
            echo 'none', PHP_EOL;
        }

        echo PHP_EOL;
        echo 'Extension-global constants:', PHP_EOL;

        if ($re->getConstants()) {
            foreach ($re->getConstants() as $name => $value) {
                echo "$name = ", var_export($value, true), PHP_EOL;
            }
        } else {
            echo 'none', PHP_EOL;
        }

        echo PHP_EOL;
        echo 'Extension-global classes:', PHP_EOL;

        if ($re->getClasses()) {
            foreach ($re->getClasses() as $rc) {
                $this->dumpClass($rc);
                echo PHP_EOL;
            }
        } else {
            echo 'none', PHP_EOL;
        }


    }

    protected function dumpClass(ReflectionClass $rc)
    {
        if ($rc->isTrait()) {
            echo 'trait ';
        } elseif ($rc->isInterface()) {
            echo 'interface ';
        } else {
            if ($rc->isAbstract()) {
                echo 'abstract ';
            }

            if ($rc->isFinal()) {
                echo 'final ';
            }

            echo 'class ';
        }

        echo $rc->getName(), PHP_EOL;

        if ($rc->getParentClass()) {
            echo '    extends ', $rc->getParentClass()->getName(), PHP_EOL;
        }

        foreach ($rc->getInterfaces() as $ri) {
            echo '    implements ', $ri->getName(), PHP_EOL;
        }

        foreach ($rc->getTraits() as $rt) {
            echo '    use ', $rt->getName(), PHP_EOL;
        }

        foreach ($rc->getConstants() as $name => $value) {
            echo "    const {$name} = ", var_export($value, true), PHP_EOL;
        }

        foreach ($rc->getProperties() as $rp) {
            if ($rp->getDeclaringClass() != $rc) {
                continue;
            }

            echo '    ';

            if ($rp->isStatic()) {
                echo 'static ';
            }


            if ($rp->isPublic()) {
                echo 'public ';
            }

            if ($rp->isProtected()) {
                echo 'protected ';
            }

            if ($rp->isPrivate()) {
                echo 'private ';
            }

            echo '$', $rp->getName();
            echo PHP_EOL;
        }



        foreach ($rc->getMethods() as $rm) {
            if ($rm->getDeclaringClass() != $rc) {
                continue;
            }

            echo '    ';

            if ($rm->isAbstract()) {
                echo 'abstract ';
            }

            if ($rm->isFinal()) {
                echo 'final ';
            }

            if ($rm->isPublic()) {
                echo 'public ';
            }

            if ($rm->isProtected()) {
                echo 'protected ';
            }

            if ($rm->isPrivate()) {
                echo 'private ';
            }

            if ($rm->isStatic()) {
                echo 'static ';
            }

            echo 'function ', $rm->getName();
            echo $this->dumpPartialFunction($rm), PHP_EOL;
        }
    }

    protected function dumpFunction(ReflectionFunction $rf)
    {
        if ($rf->inNamespace()) {
            echo $rf->getNamespaceName(), ': ';
        }

        echo 'function ', $rf->getName();

        echo $this->dumpPartialFunction($rf);
        echo PHP_EOL;
    }

    protected function dumpPartialFunction(ReflectionFunctionAbstract $rf)
    {
        $ret = '(';

        $parameters = [];
        foreach ($rf->getParameters() as $parameter) {
            $parameters[] = $this->dumpParameter($parameter);
        }

        $ret .= implode(', ', $parameters);

        $ret .= ')';

        if ($rf->hasReturnType()) {
            $ret .= ': ' . ($rf->getReturnType()->allowsNull() ? '?' : '') . $rf->getReturnType();
        }

        return $ret;
    }

    protected function dumpParameter(ReflectionParameter $rp)
    {
        $ret = [];

        $ret[] = $rp->hasType() ? ($rp->allowsNull() ? '?' : '') . (string)$rp->getType() : null;
        $ret[] = ($rp->isVariadic() ? '...' : '') . "\${$rp->getName()}";

        // $ret[] = $rp->isOptional() ? '= ?' : '';

        return trim(implode(' ', $ret));
    }
}

$d = new Dumper();

$d->dumpExtension();

?>
--EXPECT--
Extension-global functions:
Ref: function Ref\refcounted($value): bool
Ref: function Ref\refcount(object $object): int
Ref: function Ref\softrefcounted(object $object): bool
Ref: function Ref\softrefcount(object $object): int
Ref: function Ref\softrefs(object $object): array
Ref: function Ref\weakrefcounted(object $object): bool
Ref: function Ref\weakrefcount(object $object): int
Ref: function Ref\weakrefs(object $object): array
Ref: function Ref\object_handle(object $object): int
Ref: function Ref\is_obj_destructor_called(object $object): bool

Extension-global constants:
none

Extension-global classes:
class Ref\NotifierException
    extends Exception
    implements Throwable
    private $exceptions
    public function __construct($message, $exceptions, $code, $previous)
    public function getExceptions()

abstract class Ref\AbstractReference
    public function __construct(object $referent, ?callable $notify)
    public function get()
    public function valid(): bool
    public function notifier(?callable $notify): ?callable

class Ref\SoftReference
    extends Ref\AbstractReference

class Ref\WeakReference
    extends Ref\AbstractReference
