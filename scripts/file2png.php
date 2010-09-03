<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 Alter Way Solutions (France)                      |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Author: Damien Seguy <damien.seguy@gmail.com>                        |
   |                                                                      |
   +----------------------------------------------------------------------+
 */

define('SCALE',30);
$OPTIONS = array('ignore_ext' => array(), 'limit' => 0, 'ignore_dirs' => array(), );

$base = new pdo('mysql:dbname=analyseur;host=127.0.0.1','root','');
$res = $base->query("select concat(fichier,';',group_concat(distinct element order by element)) AS file from ach_rapport where module='mvc' GROUP BY fichier ORDER BY fichier");
while($row = $res->fetch()) {
    $a[] = $row['file'];
}
$a = path2array($a);


//$a = array(array(array(array(1,2,3,4,array(5,6,7),8,array(9,10,11,12)))));
//$a = array(1,2,3,4,array(5,6,7),8,array(9,10,11,12));
print_r($a);
//$a = array(1,2,3,array(4,5,6),array(7,8,10,array(11,12)),9, array(13,14,15, array(16,17,18,19,20)), 21, 22, range(23,3));
//$a = array(1,array(7,8,10,array(11,12)));
//$a = array(array(1,2,3,9));
// largeur : 4
// longueur 5

//print_r($a);

print "deep : ".deep($a)."\n";
print "large : ".large($a)."\n";

$img = imagecreatetruecolor((large($a)) * SCALE, (deep($a) ) * SCALE);
$white = imagecolorallocate($img, 0xff, 0xff, 0xff);
imagefilledrectangle($img, 0, 0, (large($a)) * SCALE -1, (deep($a) ) * SCALE -1, $white);
$black = imagecolorallocate($img, 0, 0, 0);
$red = imagecolorallocate($img, 0xff, 0, 0);
imagerectangle($img, 0, 0, (large($a)) * SCALE -1, (deep($a) ) * SCALE -1, $black);

$img = black($img, $a);
imagepng($img, './file2png.png');

function black($img, $array, &$x_dir = 0, $y_dir = 1) {
    $black = imagecolorallocate($img, 0, 0, 0);
    $red = imagecolorallocate($img, 0xff, 0, 0);
    
    $init = $x_dir; 
    $x_leaf = $x_dir;
    $y_leaf = $y_dir;
    
    $white = imagecolorallocate($img, 0xFF, 0xFF, 0xFF);
    foreach($array as $a) {
        if (!is_array($a)) {
            $color = color($img, $a);
            
            imagefilledrectangle($img, $x_leaf * SCALE, $y_leaf * SCALE, ($x_leaf + 1) * SCALE, ($y_leaf + 1) * SCALE, $color);
            imagerectangle($img, $x_leaf * SCALE, $y_leaf * SCALE, ($x_leaf + 1) * SCALE, ($y_leaf + 1) * SCALE, $white);
            $y_leaf++;
        }
    }
    
    if ($y_leaf > $y_dir) { $x_dir++; }
    foreach($array as $a) {
        if (is_array($a)) {
            
            // recursive
            $y_dir++;
            black($img, $a, $x_dir, $y_dir);
            $y_dir--;
            // @note go on
        } 
    }
    
    $end = $x_dir + 1; 

// folder

    $color = imagecolorallocate($img, rand(0,255),0,0);
    imagefilledrectangle($img, $init * SCALE, ($y_dir - 1) * SCALE, ($end -2 ) * SCALE , ($y_dir ) * SCALE, $white);
    imagerectangle($img, $init * SCALE, ($y_dir - 1) * SCALE, ($end -1) * SCALE - 1, ($y_dir ) * SCALE, $red);

    return $img;
}

function color($img, $a) {
//    return imagecolorallocate($img, 0, rand(0, 255), 0);
    if (is_array($a)) {  return imagecolorallocate($img,  0x77,0x77,0x77);}

    global $colors;
    if (!isset($colors)) {
        $colors = array('controler' => imagecolorallocate($img, 255, 0, 0),
                        'template' => imagecolorallocate($img,  0,255, 0),
                        'model' => imagecolorallocate($img,  0, 0, 255),
                        'undecided' => imagecolorallocate($img,  0x0, 0xff, 0xff),
                        'controler,template' => imagecolorallocate($img, 255, 255, 0),
                    );
    
    }
    list($file, $color) = explode(';', $a);
    return $colors[$color];
}

function large($array) {
    $large = 0;
    $leafs = 0;
    foreach($array as $a) {
        if (is_array($a)) {
            $large += large($a);
        } else {
//            $large++;
            $leafs++;
        }
    }
    
    if ($leafs > 0) {
        $large++;
    }
    
    return $large;
}

function deep($array, $level = 0) {
    $depth = 1;
    
    $max = 0;
    $leafs = 0;
    foreach($array as $a) {
        if (is_array($a)) {
            $d = deep($a, $level + 1);
            if ($d > $max) {$max = $d; }
        } else {
            $leafs++;
        }
    }
    
    if ($leafs > $max + 1) {
        $depth = $leafs + 1;
    } else {
        $depth = $max + 1;
    }

    return $depth;
}

function liste_directories_recursive( $path = '.', $level = 0 ){ 
    global $OPTIONS;
    $ignore_dirs = array_merge(array( 'cgi-bin', '.', '..' ), $OPTIONS['ignore_dirs']); 

    $dh = opendir( $path ); 
    if (!is_resource($dh)) { return array(); }
    $retour = array();
    while( false !== ( $file = readdir( $dh ) ) ) { 
        if( $file[0] == '.'                ){ continue; }

        if( is_dir( "$path/$file" ) ){ 
            if( in_array( $file, $ignore_dirs )){ continue; }
            $r = Liste_directories_recursive( "$path/$file", ($level+1) ); 
            $retour = array_merge($retour, $r);
        } else { 
            $details = pathinfo($file);
            if (!isset($details['extension'])){
                $details['extension'] = '';
            }
            if (in_array($details['extension'], $OPTIONS['ignore_ext'])) { continue; }
        
            $retour[] = "$path/$file";
        }
        if ($OPTIONS['limit'] > 0 && count($retour) >= $OPTIONS['limit']) {
            return $retour;
        }
    } 
     
    closedir( $dh ); 
    return $retour;
}

function path2array($paths) {
    $retour = array();
    
    foreach($paths as $path) {
        $dirs = explode('/', $path);
        $retour = array_merge_recursive($retour, array2multi($dirs));
    }
    
    return $retour;
}

function array2multi($array) {
    $f = array_shift($array);
    if (count($array) > 0) {
        $retour[$f] = array2multi($array);
    } else {
        $retour = array($f => $f);
    }
    
    return $retour;
}

?>