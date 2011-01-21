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

// @note must be included first
$includes = array_flip(glob('prepare/*.php'));
unset($includes['prepare/common.php']);
unset($includes['prepare/analyseur.php']);

$firsts = array('prepare/token.php',
                'prepare/processedToken.php',
                'prepare/instruction.php',
                'prepare/analyseur_regex.php',
                'prepare/variable.php',
                );
foreach($firsts as $file) {
    include($file);
    unset($includes[$file]);
}
$includes = array_keys($includes);

// @note including everything is faster than JIT
foreach($includes as $include) {
    include($include);
}
?>