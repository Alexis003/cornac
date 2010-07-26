<?php

function x ($arg) {
    $local = 1;
    global $global;
    $global = 2;
    
    return $return;
}

function x2 ($arg2) {
    $local2 = 1;
    global $global2;
    $global2 = 2;
    
    return $return2;
}

function x3 ($arg3) {
    $local3 = 1;
    global $global3;
    $global3 = 2;
    
    return $return3;
}

?>