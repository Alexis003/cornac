<?php

// read option in command line

/**
  * Read options with an expected value
  */
function get_arg_value(&$args, $option=null, $default_value=null) {
    if ($id = array_search($option, $args)) {
        $return = $args[$id + 1];
        unset($args[$id]);
        unset($args[$id + 1]);
    } else {
        $return = $default_value;
    }
    return $return; 
}

/**
  * Read options with no value
  */
function get_arg(&$args, $option) {
    if ($id = array_search($option, $args)) {
        unset($args[$id]);
        $return = true;
    } else {
        $return = false;
    }
    return $return; 
}

?>