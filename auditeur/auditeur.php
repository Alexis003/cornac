<?php

$mysql = new pdo('mysql:dbname=analyseur;host=127.0.0.1','root','');

include 'classes/sommaire.php';
$sommaire = new sommaire();

include 'classes/modules.php';
//include 'classes/modules_fonctions.php';
include 'classes/functioncalls.php';
include 'classes/typecalls.php';

$modules = array(
                 'variables',
                 
                 'emptyfunctions',
                 'classes',
                 'deffunctions',
                 
                 'functions_frequency',
// dot (ou gex...)
                 'inclusions2',
                 'classes_hierarchie',
                 );

$modules = array('constantes',
                 'evals',
                 'globals',
                 'arobases',
                 'vardump',
                 'headers',
                 'dieexit',
                 'inclusions',
                 'dir_functions',
                 'file_functions');

$modules = array('variables');
//$modules = array('classes_hierarchie');

foreach($modules as $module) {
    include('classes/'.$module.'.php');
    
    $x = new $module($mysql);
    $x->analyse();
    $x->sauve(); 

    $sommaire->add($x);
}

$sommaire->sauve();

?>