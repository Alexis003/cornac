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
$x = 3;

$y = 2 + 4;
$z = 'a'. __FILE__.'b'.true."c";

$a = 1 && 2;

$b = ('e' == 3);

$c = strtolower(true);

$d = $e(false);

$f = g(1,2,3,4,5);

$h = array(1,2,3);

$i = array('e' => true);

$j = $l["l"].' ('.$m["n"].')';
$j = ' ('.m.')';
$j = ' ('.m::n.')';

$x = (VAL ? 'km' : 'mois');


?>