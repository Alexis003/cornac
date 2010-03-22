<?php

$mysql = new pdo('mysql:dbname=analyseur;host=127.0.0.1','root','');
include('classes/rendu.php');

$rendu = new rendu($mysql);
//print $rendu->rendu(3085, 3098, 'References/optima4/include/Refs/Reference.inc');
print $rendu->rendu(6409, 6422, 'References/optima4/include/Refs/Reference.inc');

?>