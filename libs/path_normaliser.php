<?php

function path_normaliser($root, $path) {
    if ($root == '') { return $path; }
    
    if (substr($root, 0, 5) == substr($path, 0, 5)) {
        return $path;
    }
    
    if (substr($root, -1) == '/') {
        $path = $root.$path;
    } else {
        $path = $root.'/'.$path;
    }
    
    $n = 0;
    while(strpos($path, '..') !== false) {
        $path = preg_replace('#/[^\/]+/../#','/',$path);
        $path = preg_replace('#[^\/]+/../#','',$path);
        $n++;
        if ($n == 100) { return $path." (aborting : 100 reached) "; }
    }
    
    while(strpos($path, '/./') !== false) {
        $path = preg_replace('$/./$','/',$path);
    }
    
    return $path;
}

?>