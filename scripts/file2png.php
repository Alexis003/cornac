<?php

define('SCALE',30);
$OPTIONS = array('ignore_ext' => array(), 'limit' => 0, 'ignore_dirs' => array(), );

$base = new pdo('mysql:dbname=analyseur;host=127.0.0.1','root','');
$res = $base->query("select concat(fichier,';',group_concat(distinct element order by element)) AS file from ach_rapport where module='mvc' GROUP BY fichier ORDER BY fichier");
while($row = $res->fetch()) {
    $a[] = $row['file'];
}
$a = path2array($a);

$img = imagecreatetruecolor((large($a)) * SCALE, (deep($a) + 1) * SCALE);
$white = imagecolorallocate($img, 0xff, 0xff, 0xff);
imagefilledrectangle($img, 0, 0, (large($a)) * SCALE -1, (deep($a) + 1) * SCALE -1, $white);
$black = imagecolorallocate($img, 0, 0, 0);
imagerectangle($img, 0, 0, (large($a)) * SCALE -1, (deep($a) + 1) * SCALE -1, $black);

$img = black($img, $a);
imagepng($img, './file2png.png');

function black($img, $array, &$x = 0, $y = 1) {
    $black = imagecolorallocate($img, 0, 0, 0);
    
    $init = $x; 
    
    $white = imagecolorallocate($img, 0xFF, 0xFF, 0xFF);
    foreach($array as $a) {
        if (is_array($a)) {
            
            black($img, $a, $x, $y + 1);
            // @note go on
        } else {
            $color = color($img, $a);

            imagefilledrectangle($img, $x * SCALE, $y * SCALE, ($x + 1) * SCALE, ($y + 1) * SCALE, $color);
            imagerectangle($img, $x * SCALE, $y * SCALE, ($x + 1) * SCALE, ($y + 1) * SCALE, $white);
            $x++;
        }
    }
    
    $end = $x; 

    $color = color($img, $array);
    imagefilledrectangle($img, $init * SCALE, ($y - 1) * SCALE, ($end) * SCALE, ($y ) * SCALE, $white);
    imagerectangle($img, $init * SCALE, ($y - 1) * SCALE, ($end) * SCALE, ($y ) * SCALE, $black);
    
    return $img;
}

function color($img, $a) {
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
    
    $max = 0;
    foreach($array as $a) {
        if (is_array($a)) {
            deep($a);
            $d = deep($a);
            if ($d > $max) {$max = $d; }
        }
    }
    
    $depth += $max;
    
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