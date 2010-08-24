<?php

function arglist_def_2($x, $y) {}
arglist_def_2(1,2);
arglist_def_2(1,2,3);

function arglist_def_ok($x) {}
arglist_def_ok(1);

function arglist_def_ok_2($x, $y=2) {}
arglist_def_ok_2(1);
arglist_def_ok_2(1,2);

?>
