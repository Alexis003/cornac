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

$OPTIONS = array('ignore_ext' => array(), 'limit' => 0, 'ignore_dirs' => array(), );

include('../libs/file2png.php');
include('../libs/write_ini_file.php');

$base = new pdo('mysql:dbname=analyseur;host=127.0.0.1','root','');
$res = $base->query("SELECT concat(fichier,';',if (sum(if (module='dieexit', 1,0)) > 0,'black','white')) AS file,
if (sum(if (module='dieexit', 1,0)) > 0,'black','white') as OK,
sum(if (module='dieexit', 1,0)) as module
    FROM dotclear_rapport GROUP BY fichier ORDER BY module");
$a = pdo_fetch_one_col($res);

$image = new file2png();
$image->setArray($a);
$image->process();
$image->save('./file2png.png');

?>