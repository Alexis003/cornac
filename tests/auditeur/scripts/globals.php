<?php

$global_du_main_without_global;
$GLOBALS['hors_x'] = 1;

function x ()  {
    global $global_de_scope;
    $global_de_scope;

    global $g1, $g2, $g3;
    
    $local_de_scope;
    
    $GLOBALS['dans_x'] = 1;
}

class theclasse {
    var $var_not_global;
}
?>