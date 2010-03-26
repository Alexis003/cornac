<?php

$mysql = new pdo('mysql:dbname=analyseur;host=127.0.0.1','root','');

include 'classes/sommaire.php';
$sommaire = new sommaire();

include 'classes/modules.php';
include 'classes/modules_fonctions.php';

$modules = array('variables',
                 'constantes',
                 'evals',
                 'dieexit',
                 'vardump',
                 'inclusions',
                 'emptyfunctions',
                 'deffunctions',
                 'dir_functions',
                 'file_functions',
                 'headers',
                 'functions_frequency',
                 'globals',
                 'inclusions2',
                 'classes',
                 'classes_hierarchie',
                 );

$modules = array('globals');
//$modules = array('classes');
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