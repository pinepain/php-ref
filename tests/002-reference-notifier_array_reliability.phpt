--TEST--
Weak\Reference - array notifier
--SKIPIF--
<?php if (!extension_loaded("weak")) {
    print "skip";
} ?>
--FILE--
<?php

/** @var \Testsuite $helper */
$helper = require '.testsuite.php';

$obj = new stdClass();

$notifier = [];
$wr1 = new Weak\Reference($obj, $notifier);
$wr2 = new Weak\Reference($obj, function () {
    throw new Exception('Testing array notifier reliability');
});
$wr2 = new Weak\Reference($obj, $notifier);


$helper->dump($notifier);

try {
    $obj = null;
} catch (Exception $e) {
    $helper->exception_export($e);
}

$helper->dump($notifier);

$wr1 = null;

$helper->dump($notifier);

$helper->line();
?>
EOF
--EXPECT--
array(0) refcount(5){
}
array(2) refcount(5){
  [0]=>
  object(Weak\Reference)#6 (2) refcount(2){
    ["referent":"Weak\Reference":private]=>
    NULL
    ["notifier":"Weak\Reference":private]=>
    array(2) refcount(6){
      [0]=>
      object(Weak\Reference)#6 (2) refcount(2){
        ["referent":"Weak\Reference":private]=>
        NULL
        ["notifier":"Weak\Reference":private]=>
        array(2) refcount(7){
          [0]=>
          object(Weak\Reference)#6 (2) refcount(2){
            ["referent":"Weak\Reference":private]=>
            NULL
            ["notifier":"Weak\Reference":private]=>
            *RECURSION*
          }
          [1]=>
          object(Weak\Reference)#3 (2) refcount(2){
            ["referent":"Weak\Reference":private]=>
            NULL
            ["notifier":"Weak\Reference":private]=>
            *RECURSION*
          }
        }
      }
      [1]=>
      object(Weak\Reference)#3 (2) refcount(2){
        ["referent":"Weak\Reference":private]=>
        NULL
        ["notifier":"Weak\Reference":private]=>
        array(2) refcount(7){
          [0]=>
          object(Weak\Reference)#6 (2) refcount(2){
            ["referent":"Weak\Reference":private]=>
            NULL
            ["notifier":"Weak\Reference":private]=>
            *RECURSION*
          }
          [1]=>
          object(Weak\Reference)#3 (2) refcount(2){
            ["referent":"Weak\Reference":private]=>
            NULL
            ["notifier":"Weak\Reference":private]=>
            *RECURSION*
          }
        }
      }
    }
  }
  [1]=>
  object(Weak\Reference)#3 (2) refcount(2){
    ["referent":"Weak\Reference":private]=>
    NULL
    ["notifier":"Weak\Reference":private]=>
    array(2) refcount(6){
      [0]=>
      object(Weak\Reference)#6 (2) refcount(2){
        ["referent":"Weak\Reference":private]=>
        NULL
        ["notifier":"Weak\Reference":private]=>
        array(2) refcount(7){
          [0]=>
          object(Weak\Reference)#6 (2) refcount(2){
            ["referent":"Weak\Reference":private]=>
            NULL
            ["notifier":"Weak\Reference":private]=>
            *RECURSION*
          }
          [1]=>
          object(Weak\Reference)#3 (2) refcount(2){
            ["referent":"Weak\Reference":private]=>
            NULL
            ["notifier":"Weak\Reference":private]=>
            *RECURSION*
          }
        }
      }
      [1]=>
      object(Weak\Reference)#3 (2) refcount(2){
        ["referent":"Weak\Reference":private]=>
        NULL
        ["notifier":"Weak\Reference":private]=>
        array(2) refcount(7){
          [0]=>
          object(Weak\Reference)#6 (2) refcount(2){
            ["referent":"Weak\Reference":private]=>
            NULL
            ["notifier":"Weak\Reference":private]=>
            *RECURSION*
          }
          [1]=>
          object(Weak\Reference)#3 (2) refcount(2){
            ["referent":"Weak\Reference":private]=>
            NULL
            ["notifier":"Weak\Reference":private]=>
            *RECURSION*
          }
        }
      }
    }
  }
}
array(2) refcount(5){
  [0]=>
  object(Weak\Reference)#6 (2) refcount(2){
    ["referent":"Weak\Reference":private]=>
    NULL
    ["notifier":"Weak\Reference":private]=>
    array(2) refcount(6){
      [0]=>
      object(Weak\Reference)#6 (2) refcount(2){
        ["referent":"Weak\Reference":private]=>
        NULL
        ["notifier":"Weak\Reference":private]=>
        array(2) refcount(7){
          [0]=>
          object(Weak\Reference)#6 (2) refcount(2){
            ["referent":"Weak\Reference":private]=>
            NULL
            ["notifier":"Weak\Reference":private]=>
            *RECURSION*
          }
          [1]=>
          object(Weak\Reference)#3 (2) refcount(1){
            ["referent":"Weak\Reference":private]=>
            NULL
            ["notifier":"Weak\Reference":private]=>
            *RECURSION*
          }
        }
      }
      [1]=>
      object(Weak\Reference)#3 (2) refcount(1){
        ["referent":"Weak\Reference":private]=>
        NULL
        ["notifier":"Weak\Reference":private]=>
        array(2) refcount(7){
          [0]=>
          object(Weak\Reference)#6 (2) refcount(2){
            ["referent":"Weak\Reference":private]=>
            NULL
            ["notifier":"Weak\Reference":private]=>
            *RECURSION*
          }
          [1]=>
          object(Weak\Reference)#3 (2) refcount(1){
            ["referent":"Weak\Reference":private]=>
            NULL
            ["notifier":"Weak\Reference":private]=>
            *RECURSION*
          }
        }
      }
    }
  }
  [1]=>
  object(Weak\Reference)#3 (2) refcount(1){
    ["referent":"Weak\Reference":private]=>
    NULL
    ["notifier":"Weak\Reference":private]=>
    array(2) refcount(6){
      [0]=>
      object(Weak\Reference)#6 (2) refcount(2){
        ["referent":"Weak\Reference":private]=>
        NULL
        ["notifier":"Weak\Reference":private]=>
        array(2) refcount(7){
          [0]=>
          object(Weak\Reference)#6 (2) refcount(2){
            ["referent":"Weak\Reference":private]=>
            NULL
            ["notifier":"Weak\Reference":private]=>
            *RECURSION*
          }
          [1]=>
          object(Weak\Reference)#3 (2) refcount(1){
            ["referent":"Weak\Reference":private]=>
            NULL
            ["notifier":"Weak\Reference":private]=>
            *RECURSION*
          }
        }
      }
      [1]=>
      object(Weak\Reference)#3 (2) refcount(1){
        ["referent":"Weak\Reference":private]=>
        NULL
        ["notifier":"Weak\Reference":private]=>
        array(2) refcount(7){
          [0]=>
          object(Weak\Reference)#6 (2) refcount(2){
            ["referent":"Weak\Reference":private]=>
            NULL
            ["notifier":"Weak\Reference":private]=>
            *RECURSION*
          }
          [1]=>
          object(Weak\Reference)#3 (2) refcount(1){
            ["referent":"Weak\Reference":private]=>
            NULL
            ["notifier":"Weak\Reference":private]=>
            *RECURSION*
          }
        }
      }
    }
  }
}

EOF
