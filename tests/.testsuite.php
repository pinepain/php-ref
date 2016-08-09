<?php


function exception_error_handler($errno, $errstr, $errfile, $errline)
{
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

set_error_handler('exception_error_handler');

class Testsuite
{
    public function header($title)
    {
        echo $title, ':', PHP_EOL;

        echo str_repeat('-', strlen($title) + 1), PHP_EOL;
    }

    public function space($new_lines_number = 2)
    {
        echo str_repeat(PHP_EOL, max(1, $new_lines_number));
    }

    public function line()
    {
        $this->space(1);
    }

    public function exception_export(\Throwable $e)
    {
        echo get_class($e), ': ', $e->getMessage(), PHP_EOL;
    }

    public function ref_exception_export(\Ref\NotifierException $e)
    {
        $this->exception_export($e);

        if ($e->getPrevious()) {
            echo 'previous: ';
            $this->exception_export($e->getPrevious());
        }

        if (!empty($e->getExceptions())) {
            echo 'thrown:', PHP_EOL;
            foreach ($e->getExceptions() as $id => $thrown) {
                echo '    #' . $id . ': ';
                $this->exception_export($thrown);
                if ($thrown->getPrevious()) {
                    echo '        previous: ', $this->exception_export($thrown->getPrevious());
                }
            }
        }
    }

    public function export($value)
    {
        echo gettype($value), ': ', var_export($value, true), PHP_EOL;
    }

    public function export_annotated($message, $value)
    {
        echo $message, ': ';
        $this->export($value);
    }

    public function dump($value, bool $detailed = true)
    {
        $detailed ? debug_zval_dump($value) : var_dump($value);
    }

    public function dump_annotated($message, $value, bool $detailed = true)
    {
        echo $message, ': ';
        $this->dump($value, $detailed);
    }

    public function object_type($object)
    {
        echo get_class($object), PHP_EOL;
    }

    public function function_export($func, array $args = [])
    {
        echo $func, '(): ', var_export(call_user_func_array($func, $args), true), PHP_EOL;
    }

    public function method_export($object, $method, array $args = [])
    {
        echo get_class($object), '::', $method, '(): ', var_export(call_user_func_array([$object, $method], $args), true), PHP_EOL;
    }

    public function assert($message, $actual, $expected = true, $identical = true)
    {
        echo $message, ': ';

        if ($identical) {
            echo($expected === $actual ? 'ok' : 'failed'), PHP_EOL;
        } else {
            echo($expected == $actual ? 'ok' : 'failed'), PHP_EOL;
        }
    }

    public function value_matches($expected, $actual, $identical = true)
    {
        if ($identical) {
            echo 'Expected ', var_export($expected, true), ' value is ', ($expected === $actual ? 'identical to' : 'not identical to'), ' actual value ', var_export($actual, true), PHP_EOL;
        } else {
            echo 'Expected ', var_export($expected, true), ' value is ', ($expected == $actual ? 'matches' : 'doesn\'t match'), ' actual value ', var_export($actual, true), PHP_EOL;
        }
    }

    public function value_instanceof($value, $expected)
    {
        echo 'Value', ($value instanceof $expected ? ' is' : ' not an'), ' instance of ', $expected, PHP_EOL;
    }

    public function value_matches_with_no_output($expected, $actual, $identical = true)
    {
        if ($identical) {
            echo 'Expected value is ', ($expected === $actual ? 'identical to' : 'not identical to'), ' actual value', PHP_EOL;
        } else {
            echo 'Expected value ', ($expected == $actual ? 'matches' : 'doesn\'t match'), ' actual value', PHP_EOL;
        }
    }

    public function method_matches($object, $method, $expected, array $args = [])
    {
        echo get_class($object), '::', $method, '()', ' ', ($expected === call_user_func_array([$object, $method], $args) ? 'matches' : 'doesn\'t match'), ' expected value', PHP_EOL;
    }

    public function method_throws($object, $method, $exception, $message = null, array $args = [])
    {
        try {
            call_user_func_array([$object, $method], $args);
        } catch (\Throwable $e) {

            if ($e instanceof $exception) {

                if ($message !== null) {
                    if ($message == $e->getMessage()) {
                        echo get_class($object), '::', $method, '()', ' throw expected exception and messages match', PHP_EOL;
                    } else {
                        echo get_class($object), '::', $method, '()', ' throw expected exception, but messages doesn\'t match', PHP_EOL;
                    }
                } else {
                    echo get_class($object), '::', $method, '()', ' throw expected exception', PHP_EOL;
                }
            } else {
                echo get_class($object), '::', $method, '()', ' throw unexpected exception', PHP_EOL;
            }

            return;
        }

        echo get_class($object), '::', $method, '()', ' ', 'doesn\'t throw any exception', PHP_EOL;

    }


    public function method_matches_instanceof($object, $method, $expected)
    {
        echo get_class($object), '::', $method, '() result', ($object->$method() instanceof $expected ? ' is' : ' not an'), ' instance of ', $expected, PHP_EOL;
    }


    public function method_matches_with_output($object, $method, $expected)
    {
        echo get_class($object), '::', $method, '()', ' ', ($expected === $object->$method() ? 'matches' : 'doesn\'t match'), ' expected ', var_export($expected, true), PHP_EOL;
    }

    public function class_constants_export($class_or_object)
    {
        $refl = new ReflectionClass($class_or_object);

        $class_name = $refl->getName();
        foreach ($refl->getConstants() as $name => $value) {
            echo $class_name, '::', $name, ' = ', var_export($value, true), PHP_EOL;
        }
    }
}

return new Testsuite();
