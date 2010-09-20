<?php

include('../libs/database.php');
$ini = array('mysql' => array('active' => 1,
                              'dsn' => 'mysql:dbname=analyseur;host=127.0.0.1', 
                              'username' => 'root',
                              'password' => ''),
             'cornac' => array('prefix' => 'tu' ) );
$DATABASE = new database($ini);

?>