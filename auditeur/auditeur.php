<?php

$mysql = new pdo('mysql:dbname=analyseur;host=127.0.0.1','root','');

include 'classes/sommaire.php';
$sommaire = new sommaire();

include 'classes/modules.php';
//include 'classes/modules_fonctions.php';
include 'classes/functioncalls.php';
include 'classes/typecalls.php';
include 'classes/noms.php';

$modules = array(
                 'emptyfunctions',
// dot (ou gex...)
                 '',
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
                 'file_functions',
                 'deffunctions',
                 'functions_frequency',
                 'classes',
                 'variables',
                 'classes_hierarchie',
                 'inclusions2',
                 'defconstantes',
                 );

$modules = array('classes_hierarchie',);
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