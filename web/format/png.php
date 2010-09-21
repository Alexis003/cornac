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
function get_html_check($lines) {
    get_html_level2($lines);
}

function get_html_level2($lines) {
    global $DATABASE;
    
    include('../libs/write_ini_file.php');

    $query = "SELECT DISTINCT concat(fichier,';','white') AS all_files FROM <tokens> ";
    $res = $DATABASE->query($query);
    $rows = pdo_fetch_one_col($res);
    
    include('../libs/file2png.php');
    
    $image = new file2png();
    $image->setArray($rows);
    $image->process();
    $image->save();
}

function print_entete($prefix='No Name') {

}

function print_pieddepage($prefix='No Name') {

}
?>