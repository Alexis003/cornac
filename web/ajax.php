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
if (!isset($_GET['module'])) { die(''); }

$fp = fopen('./ajax.log', 'a');
fwrite($fp, "date = ".date('r')."\n");
fwrite($fp, "element = ".@$_GET['element']."\n");
fwrite($fp, "file = ".@$_GET['file']."\n");
fwrite($fp, "elementfile = ".@$_GET['elementfile']."\n");
fclose($fp);

include('include/config.php');

$_CLEAN['module'] = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_GET['module']);
if (isset($_GET['element'])) {
    $_CLEAN['element'] = $_GET['element']; 
} else {
    $_CLEAN['element'] = NULL; 
}
$_CLEAN['elementid'] = @$_GET['elementid'] ?: NULL; 
$_CLEAN['elementfile'] = @$_GET['elementfile'] ?: NULL; 
$_CLEAN['file'] = @$_GET['file'] ?: NULL; 
$_CLEAN['reason'] = @$_GET['reason'] ?: NULL; 

if (!empty($_CLEAN['element'])) {
    $query = "SELECT element FROM <rapport> WHERE id = ".$DATABASE->quote($_CLEAN['element'])."";
    $res = $DATABASE->query($query);
    $id = $res->fetch(PDO::FETCH_NUM);
    $id = $id[0];

    $query = "UPDATE <rapport> SET checked = 1 - checked WHERE element = ".$DATABASE->quote($id)." AND module=".$DATABASE->quote($_CLEAN['module'])."";
    
    $res = $DATABASE->query($query);
    print $res->rowCount() ? 'yes' : '';
} elseif (!empty($_CLEAN['elementfile'])) {
    $query = "SELECT element, fichier FROM <rapport> WHERE id = ".$DATABASE->quote($_CLEAN['elementfile'])."";
    $res = $DATABASE->query($query);
    $id = $res->fetch(PDO::FETCH_ASSOC);
    $file = $id['fichier'];
    $element = $id['element'];

    $query = "UPDATE <rapport> SET checked = 1 - checked WHERE element = ".$DATABASE->quote($element)." AND fichier = ".$DATABASE->quote($file)." AND module=".$DATABASE->quote($_CLEAN['module'])."";
    
    $res = $DATABASE->query($query);
    print $res->rowCount() ? 'yes' : '';
} elseif (!empty($_CLEAN['file'])) {
    $query = "UPDATE <rapport> SET checked = 1 - checked WHERE fichier = ".$DATABASE->quote($_CLEAN['file'])." AND module=".$DATABASE->quote($_CLEAN['module'])."";
    
    $res = $DATABASE->query($query);
    print $res->rowCount() ? 'yes' : '';
} elseif (!empty($_CLEAN['elementid'])) {
    $query = "UPDATE <rapport> SET checked = 1 - checked WHERE id = ".$DATABASE->quote($_CLEAN['elementid'])." AND module=".$DATABASE->quote($_CLEAN['module'])."";    
    $res = $DATABASE->query($query);

    if (is_int($_CLEAN['reason'])) {
        $query = "UPDATE cnc_daily SET reason_id = ".$DATABASE->quote($_CLEAN['reason'])."  WHERE report_id = ".$DATABASE->quote($_CLEAN['elementid'])." AND module=".$DATABASE->quote($_CLEAN['module'])."";
        $res = $DATABASE->query($query);
    } else {
        $query = "INSERT INTO cnc_reasons VALUES (NULL, ".$DATABASE->quote($_CLEAN['reason']).")";
        $res = $DATABASE->query($query);
        $id = $DATABASE->insert_id();
        
        $query = "UPDATE cnc_daily SET reason_id = ".$id."  WHERE report_id = ".$DATABASE->quote($_CLEAN['elementid'])." AND module=".$DATABASE->quote($_CLEAN['module'])."";
        $res = $DATABASE->query($query);
    }

    print $res->rowCount() ? 'yes' : '';
} else {
    print 'ko';
} 

?>