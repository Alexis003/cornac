<?php

$a = 3;
$c = array('d'=>'a', 'e' =>2);

x();

function x() {
    global $$c['d'];
    
    print_r($c);
}
?>