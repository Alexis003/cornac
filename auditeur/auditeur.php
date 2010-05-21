<?php

$mysql = new pdo('mysql:dbname=analyseur;host=127.0.0.1','root','');

include 'classes/sommaire.php';
$sommaire = new sommaire();

include 'classes/modules.php';
include 'classes/functioncalls.php';
include 'classes/typecalls.php';
include 'classes/noms.php';

$modules = array(
                 'arobases',
                 'classes_hierarchie',
                 'classes',
                 'constantes',
                 'defconstantes',
                 'deffunctions',
                 'dieexit',
                 'dir_functions',
                 'emptyfunctions',
                 'execs',
                 'evals',
                 'file_functions',
                 'functions_frequency',
                 'globals',
                 'headers',
                 'ifsanselse',
                 'inclusions',
                 'inclusions2',
                 'php_functions',
                 'parentheses',
                 'vardump',
                 'variables',
                 'gpc',
                 'mysql_functions',
                 'mysqli_functions',
                 'ldap_functions',
                 'sql_queries',
                 'xml_functions',
                 'image_functions',
                 'xml_functions',
                 'session_functions',
                 'secu_protection_functions',
                 'regex',
                 'filter_functions',
                 'methodscall',
                 );
/*
$modules = array(                 'xml_functions',
                 'session_functions',
                 'secu_protection_functions',
                 'regex',
                 'filter_functions',
);*/
$modules = array('classes_hierarchie',);


foreach($modules as $module) {
    print "+ $module\n";
    include_once('classes/'.$module.'.php');
    
    $x = new $module($mysql);
    $x->analyse();
    $x->sauve(); 

    $sommaire->add($x);
}

$sommaire->sauve();

?>