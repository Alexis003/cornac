<?php

$a = array_map('cb_1_1', range(1,3));
$b = call_user_func('cb_1_2');
$c = call_user_func_array('cb_1_3', range(1,4));

// second argument
usort($c, 'cb_2_1');
/*
                              'preg_replace_callback',
                              'uasort',
                              'uksort',
                              'array_reduce',
                              'array_walk',
                              'array_walk_recursive',
                              'mysqli_set_local_infile_handler'
                              */

// last argument
$x = array_diff_uassoc(range(1,2), range(3,4), 'cb_0_1');
$y = array_diff_ukey(range(1,2), range(3,4),range(5,6), 'cb_0_2');
/*
                   '',
                   'array_intersect_uassoc',
                   'array_intersect_ukey',
                   'array_udiff_assoc',
                   'array_udiff_uassoc',
                   'array_udiff',
                   'array_uintersect_assoc',
                   'array_uintersect_uassoc',
                   'array_uintersect',
                   'array_filter',
                   'array_reduce'
*/

?>