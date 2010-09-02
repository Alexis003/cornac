<?php

define('SCALE',30);
$a = array(1,2,3,array(4,5,6, array(7,8, array(9,10,11,array(12,13,14)))), 15, 16, array(17, 18), 19);

$img = imagecreate(large($a) * SCALE, deep($a) * SCALE);
$white = imagecolorallocate($img, 0xFF, 0xFF, 0xFF);
$black = imagecolorallocate($img, 0, 0, 0);

$img = black($img, $a);
imagepng($img, './file2png.png');

function black($img, $array, &$x = 0, $y = 1) {
    $black = imagecolorallocate($img, 0, 0, 0);
    
    $init = $x; 
    
    foreach($array as $a) {
        if (is_array($a)) {
            
            black($img, $a, $x, $y + 1);
            // go on
        } else {
            $color = color($img, $a);
            imagefilledrectangle($img, $x * SCALE, $y * SCALE, ($x + 1) * SCALE, ($y + 1) * SCALE, $color);
            $x++;
        }
    }
    
    $end = $x; 

    $color = color($img, $array);
    imagefilledrectangle($img, $init * SCALE, ($y - 1) * SCALE, ($end) * SCALE, ($y ) * SCALE, $color);
    $white = imagecolorallocate($img, 0xFF, 0xFF, 0xFF);
    imagerectangle($img, $init * SCALE, ($y - 1) * SCALE, ($end) * SCALE, ($y ) * SCALE, $white);
    
    return $img;
}

function color($img, $a) {
    return imagecolorallocate($img, 0, rand(0, 255),0);
}

function large($array) {
    $large = 0;
    foreach($array as $a) {
        if (is_array($a)) {
            $large += large($a);
        } else {
            $large ++;
        }
    }
    return $large;
}


function deep($array) {
    $depth = 1;
    
    foreach($array as $a) {
        if (is_array($a)) {
            deep($a);
            $depth += deep($a);
        }
    }
    
    return $depth;
}


?>