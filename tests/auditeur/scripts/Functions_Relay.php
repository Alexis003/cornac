<?php

function this_is_a_relay($x, $y, $z) {
    this_is_a_relay2($x, $y, $z);
}

function this_is_a_relay2($x, $y = 2, array $z) {
    echo $x++;
    this_is_a_not_relay($x, $z, $y);
    
    return true;
}

function this_is_a_not_relay($x, $z, $y) {
    
}

?>