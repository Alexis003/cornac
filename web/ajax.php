<?php

if (!isset($_GET['module'])) { die(''); }

$fp = fopen('./ajax.log', 'a');
fwrite($fp, "date = ".date('r')."\n");
fwrite($fp, "element = ".@$_GET['element']."\n");
fwrite($fp, "file = ".@$_GET['file']."\n");
fwrite($fp, "elementfile = ".@$_GET['elementfile']."\n");
fclose($fp);

include('include/config.php');

$_CLEAN['module'] = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_GET['module']);
$_CLEAN['element'] = $_GET['element'] ?: NULL; 
$_CLEAN['elementfile'] = $_GET['elementfile'] ?: NULL; 
$_CLEAN['file'] = $_GET['file'] ?: NULL; 

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
    $query = "UPDATE <rapport> SET checked = 1 - checked WHERE fichier = ".$DATABASE->quote($_CLEAN['file'])." AND module='".$DATABASE->quote($_CLEAN['module'])."'";
    
    $res = $DATABASE->query($query);
    print $res->rowCount() ? 'yes' : '';
} else {
    print '';
} 

?>