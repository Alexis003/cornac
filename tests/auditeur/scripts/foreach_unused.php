<?php

// k nor v
foreach($a as $k_variable => $v_variable) {
    
}

// OK
foreach($a as $K_variable => $V_variable) {
    $K_variable++;
    $V_variable++;
}

// k nor v as reference
foreach($a as $k_reference => &$v_reference) {
    
}

// OK
foreach($a as $K_reference => &$V_reference) {
    $K_reference++;
    $V_reference++;
}

?>