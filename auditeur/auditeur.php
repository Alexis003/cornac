<?php

if (count($argv) > 1) {
    $prefixe = $argv[1];
} else {
    $prefixe = 'tokens';
}
print "Travail avec la base $prefixe\n";

$mysql = new pdo('mysql:dbname=analyseur;host=127.0.0.1','root','');

include 'classes/sommaire.php';
$sommaire = new sommaire();

include 'classes/modules.php';
include 'classes/functioncalls.php';
include 'classes/typecalls.php';
include 'classes/noms.php';

$modules = array(
'_new',
'affectations_variables',
'appelsfonctions',
'arobases',
'classes',
'classes_hierarchie',
'constantes',
'constantes_classes',
'defconstantes',
'deffunctions',
'defmethodes',
'dieexit',
'dir_functions',
//'dot',
'emptyfunctions',
'ereg_functions',
'error_functions',
'evals',
'exec_functions',
'execs',
'file_functions',
'filter_functions',
//'functioncalls',
'functions_frequency',
'functions_undefined',
'functions_unused',
'functionscalls',
'globals',
'gpc',
'headers',
'iffectations',
'ifsanselse',
'image_functions',
'inclusions',
'inclusions2',
'ldap_functions',
'literals',
'method_special',
'methodscall',
'modules_used',
'mssql_functions',
'mysql_functions',
'mysqli_functions',
'nestedif',
'nestedloops',
//'noms',
'nonphp_functions',
'parentheses',
'php_functions',
//'php_modules',
'proprietes_publiques',
'regex',
'returns',
'secu_functions',
'secu_protection_functions',
'session_functions',
'sql_queries',
'statiques',
'tableaux',
'tableaux_gpc',
'tableaux_gpc_seuls',
'thrown',
'trim_rawtext',
'undeffunctions',
'unused_args',
'vardump',
'variables',
'variablesvariables',
'xdebug_functions',
'xml_functions',
'zfAction',
'zfController',
'zfElements',
'zfGetGPC',
                 );
/*
$modules = array(                 'xml_functions',
                 'session_functions',
                 'secu_protection_functions',
                 'regex',
                 'filter_functions',
);
$modules = array(                    'variables', 'constantes','_new',
    'affectations_variables', 'headers',
                 'method_special','globals',
                 'iffectations',
);
*/
//$modules = array('functions_undefined','functions_unused');
//$modules = array('zfGetGPC');


$modules_faits = array();
$res = $mysql->query('SELECT module FROM '.$prefixe.'_rapport_module');
$modules_faits = $res->fetchall(PDO::FETCH_COLUMN);
//print_r($modules_faits);

// init avec le contenu de la base? 

foreach($modules as $module) {
    print "+ $module\n";
    analyse_module($module);
}

function analyse_module($module) {
    include_once('classes/'.$module.'.php');
    global $modules_faits, $mysql,$sommaire;
    
    if (isset($modules_faits[$module])) {  
        continue; 
    }

    $x = new $module($mysql);
    $dependances = $x->dependsOn();
    
    
    if (count($dependances) > 0) {
        $manque = array_diff($dependances, $modules_faits);
        if (count($manque) > 0) {
            foreach($manque as $m) {
                print "  +  $m\n";
                analyse_module($m);
            }
        } else {
            print "Pas de dépendance\n";
        }
    }
    
    $x->analyse();
    $x->sauve(); 
    
    $sommaire->add($x);
    $modules_faits[$module] = 1;
}

$sommaire->sauve();

?>