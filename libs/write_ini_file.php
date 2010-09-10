<?php

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

?>