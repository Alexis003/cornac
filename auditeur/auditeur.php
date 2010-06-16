#!/usr/bin/php
<?php

if ($id = array_search('-?', $argv)) {
    print_help();
    die();
}

if ($id = array_search('-h', $argv)) {
    print_help();
    die();
}

if ($id = array_search('-help', $argv)) {
    print_help();
    die();
}

$args = $argv;
if ($id = array_search('-p', $argv)) {
    $prefixe = $args[$id + 1];
    unset($args[$id]);
    unset($args[$id + 1]);
} else {
    $prefixe = 'tokens';
}

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
'emptyfunctions',
'ereg_functions',
'error_functions',
'evals',
'exec_functions',
'execs',
'file_functions',
'filter_functions',
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
'nonphp_functions',
'parentheses',
'php_functions',
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

if ($id = array_search('-a', $argv)) {
    $m = explode(',', $args[$id + 1]);
    unset($args[$id]);
    unset($args[$id + 1]);

    $diff = array_diff($m , $modules);
    if (count($diff) > 0) {
        print count($diff)." modules are unknown, and omitted : ".join(', ', $diff)."\n";
    }

    $m = array_intersect($m, $modules);
    
    if (count($m) == 0) {
        print "No analyzer provided : aborting\n";
        die();
    } else {
        $modules = $m;
    }
} else {
    // rien 
}

print count($modules)." modules will be treated : ".join(', ', $modules)."\n";


print "Work with prefixes '$prefixe'\n";

$mysql = new pdo('mysql:dbname=analyseur;host=127.0.0.1','root','');

// rendu (templates)
include 'classes/sommaire.php';
$sommaire = new sommaire();

// abstract classes 
include 'classes/abstract/modules.php';
include 'classes/abstract/functioncalls.php';
include 'classes/abstract/typecalls.php';
include 'classes/abstract/noms.php';

// analyzers doing the real thing

/*

$modules_faits = array();
$res = $mysql->query('SELECT module FROM '.$prefixe.'_rapport_module');
$modules_faits = $res->fetchall(PDO::FETCH_COLUMN);
*/
$modules_faits = array();


// init avec le contenu de la base? 

foreach($modules as $module) {
    print "+ $module\n";
    analyse_module($module);
}

function analyse_module($module) {
    include_once('classes/'.$module.'.php');
    global $modules_faits, $mysql,$sommaire;
    
    if (isset($modules_faits[$module])) {  
        return ;
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
            print "Dépendances faites\n";
        }
    }
    
    $x->analyse();
    $x->sauve(); 
    
    $sommaire->add($x);
    $modules_faits[$module] = 1;
}

$sommaire->sauve();

function help() {
    print <<<TEXT
Usage : ./auditeur.php
prefix : tokens (default)

    -?    : this help
    -h    : this help
    -help : this help
    -p    : prefixe for the tables to be used. Default to 'tokens'
    -a    : comma separated list of analyzers to be used. Defaut to all. 
TEXT;
    
    die();
}

?>