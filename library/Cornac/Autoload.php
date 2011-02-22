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
// @configuration all constants should be gathered in one file
define('T_NAMESPACED_NAME', 500);

class Cornac_Autoload {
    static public function autoload($name) {
        // @todo path may have to be configurable
        if (basename($_SERVER['PWD']) == 'auditeur'){
        // @todo remove this by moving auditeur in bin dir. 
            $path = '../library';
        } elseif (basename($_SERVER['PWD']) == 'scripts') {
            $path = '../library';
        } elseif (isset($_SERVER['HTTP_HOST'])) {
            $path = '../library';
        } else {
            $path = 'library';
        }

        $file = $path.'/'.str_replace('_', '/', $name).'.php';
        if (file_exists($file)) {
            include($file);
        } else { 
            // @note no display here, please. May be some error handling?
        }
    }
}

?>