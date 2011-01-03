<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 - 2011 Alter Way Solutions (France)               |
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