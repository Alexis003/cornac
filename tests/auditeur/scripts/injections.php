<?php

$a = $_GET['b'];
$c = $d + $a;
$e = $c + $f;
$g = $h + $i;
$k = $g + $e;
$l = test($k);

function test($fx) {
    $fa = $fx;
    $fc = $fd + $fa;
    $fe = $fc + $ff;
    $fg = $fh + $fi;
    $fk = $fg + $fe;
    return $fk;
}
?>
