<?php

if (!isset($_GET['module'])) { die('non'); }

$fp = fopen('./ajax.log', 'a');
fwrite($fp, "element = ".@$_GET['element']."\n");
fwrite($fp, "file = ".@$_GET['file']."\n");
fclose($fp);

$mysql = new pdo('mysql:dbname=analyseur;host=127.0.0.1','root','');

if (isset($_GET['element'])) {
    $query = "SELECT element FROM ach_rapport WHERE id = ".$mysql->quote($_GET['element'])."";
    $res = $mysql->query($query);
    $id = $res->fetch(PDO::FETCH_NUM);
    $id = $id[0];

    $query = "UPDATE ach_rapport SET checked = 1 - checked WHERE element = ".$mysql->quote($id)." AND module=".$mysql->quote($_GET['module'])."";
    
    $res = $mysql->query($query);
    print $res->rowCount() ? 'oui' : 'non';
} elseif (isset($_GET['elementfile'])) {
    $query = "SELECT element, fichier FROM ach_rapport WHERE id = ".$mysql->quote($_GET['elementfile'])."";
    $res = $mysql->query($query);
    $id = $res->fetch(PDO::FETCH_ASSOC);
    $file = $id['fichier'];
    $element = $id['element'];

    $query = "UPDATE ach_rapport SET checked = 1 - checked WHERE element = ".$mysql->quote($element)." AND fichier = ".$mysql->quote($file)." AND module=".$mysql->quote($_GET['module'])."";
    
    $res = $mysql->query($query);
    print $res->rowCount() ? 'oui' : 'non';
} elseif (isset($_GET['file'])) {
    $query = "UPDATE ach_rapport SET checked = 1 - checked WHERE fichier = ".$mysql->quote($_GET['file'])." AND module='".$mysql->quote($_GET['module'])."'";
    
    $res = $mysql->query($query);
    print $res->rowCount() ? 'oui' : 'non';
} else {
    print 'non';
} 

?>