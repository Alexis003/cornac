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
   +----------------------------------------------------------------------+
 */

// by freamer89 at gmail dot com
// from PHP docs comments on http://php.net/manual/en/function.parse-ini-file.php
// renamed
function write_ini_file($array, $file)
{
    $res = array();
    foreach($array as $key => $val)
    {
        if(is_array($val))
        {
            $res[] = "[$key]";
            foreach($val as $skey => $sval) $res[] = "$skey = ".(is_numeric($sval) ? $sval : '"'.$sval.'"');
        }
        else $res[] = "$key = ".(is_numeric($val) ? $val : '"'.$val.'"');
    }
    file_put_contents($file, implode("\r\n", $res));
}

function array_join_keyval($array, $join=' => ') {
    $r = array();
    foreach($array as $k => $v) {
        $r[] = "$k$join$v";
    }
    return $r;
}

function array_explode_keyval($array, $join = ' => ') {
    $r = array();
    foreach($array as $value) {
        list($k, $v) = explode($join, $value);
        $r[$k] = $v;
    }
    return $r;
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

// merge a pdo fetchall into a simple array
function multi2array($array, $index = null) {
    $r = array();
    
    if(!is_array($array)) { return $r; }
    if(count($array) == 0) { return $r; }
    
    if (is_null($index)) {
        list($k, $v) = each($array);
        
        if (!is_array($v)) { var_dump($v); die; }
        list($index, $V) = each($v);
        
        $r[$k] = $V;
    }
    
    foreach($array as $k => $v) {
        if (isset($v[$index])) {
            $r[$k] = $v[$index];
        }
    }

    return $r;
}


// 
function array2multi($array) {
    $f = array_shift($array);
    if (count($array) > 0) {
        $retour[$f] = array2multi($array);
    } else {
        $retour = array($f => $f);
    }
    
    return $retour;
}

function pdo_fetch_one_col($res, $col = null) {
    $row = $res->fetch(PDO::FETCH_ASSOC);
    
    if (!$row) { return array(); }
    
    if (is_null($col)) {
        list($col, $foo) = each($row);
    }
    
    if (!isset($row[$col])) {
        return array();
    }
    
    $r = array($row[$col]);
    
    while($row = $res->fetch(PDO::FETCH_ASSOC)) { 
        $r[] = $row[$col];
    }
    
    return $r;
}

// recursive glob 
function rglob($path) {
    $files = glob($path);
    
    $files2 = array();
    foreach($files as $id => $file) {
        if (is_dir($file)) {
            $files2[] = $file."/";
            $files2 = array_merge($files2, rglob($file."/*"));
        } else {
            $files2[] = $file;
        }
    }
    
    return $files2;
}
/*
function liste_directories_recursive( $path = '.', $level = 0 ){ 
    $ignore = array( 'cgi-bin', '.', '..' ); 

    $dh = opendir( $path ); 
    if (!is_resource($dh)) { return array(); }
    $retour = array();
    while( false !== ( $file = readdir( $dh ) ) ){ 
        if( !in_array( $file, $ignore ) ){ 
            if( is_dir( "$path/$file" ) ){ 
                $r = Liste_directories_recursive( "$path/$file", ($level+1) ); 
                $retour = array_merge($retour, $r);
            } else { 
                $retour[] = "$path/$file";
            } 
        } 
    } 
     
    closedir( $dh ); 
    return $retour;
}*/ 
?>