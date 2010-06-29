#!/usr/bin/php
<?php

if ($id = array_search('-?', $argv)) {
    help();
    die();
}

if ($id = array_search('-h', $argv)) {
    help();
    die();
}

if ($id = array_search('-help', $argv)) {
    help();
    die();
}

if ($id = array_search('-d', $argv)) {
    define("DO_DEPENDENCES",true);
} else {
    define("DO_DEPENDENCES",false);
}

$args = $argv;
if ($id = array_search('-p', $argv)) {
    $prefixe = $args[$id + 1];
    unset($args[$id]);
    unset($args[$id + 1]);
} else {
    $prefixe = 'tokens';
}

if ($id = array_search( '-I', $argv)) {
    $ini = $argv[$id + 1];
    
    unset($argv[$id]);
    unset($argv[$id + 1]);
    
    global $INI;
    if (file_exists('../ini/'.$ini)) {
        define('INI','../ini/'.$ini);
    } elseif (file_exists('../ini/'.$ini.".ini")) {
        define('INI','../ini/'.$ini.".ini");
    } elseif (file_exists($ini)) {
        define('INI',$ini);
    } else {
        define('INI','../ini/'.'tokenizeur.ini');
    }
} else {
    define('INI','../ini/'.'tokenizeur.ini');
}

// @todo : que faire si on ne trouve même pas le .ini ? 
print "Fichier de directives : ".INI."\n";
global $INI;
$INI = parse_ini_file(INI, true);

$modules = array(
'_new',
'affectations_variables',
//'appelsfonctions',
'arobases',
'classes',
'classes_hierarchie',
'constantes',
'constantes_classes',
'defconstantes',
'deffunctions',
'doubledeffunctions',
'doubledefclass',
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
'php_modules',
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
'properties_defined',
'properties_used',
'classes_unused',
'classes_undefined',
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


if (isset($INI['mysql']) && $INI['mysql']['active'] == true) {
    $database = new pdo($INI['mysql']['dsn'],$INI['mysql']['username'], $INI['mysql']['password']);
} elseif (isset($INI['sqlite'])  && $INI['sqlite']['active'] == true) {
    $database = new pdo($INI['sqlite']['dsn']);
} else {
    print "No database configuration provided (no mysql, no sqlite)\n";
    die();
}
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
    global $modules_faits, $database,$sommaire;
    
    if (isset($modules_faits[$module])) {  
        return ;
    }

    $x = new $module($database);
    $dependances = $x->dependsOn();
    
    if (count($dependances) > 0) {
        $manque = array_diff($dependances, $modules_faits);
        if (count($manque) > 0) {
            foreach($manque as $m) {
                print "  +  $m";
                if (DO_DEPENDENCES) {
                    analyse_module($m);
                } else {
                    // @todo : check if dependances are there or not. 
                    // if not, they should be done, of course!
                    // nothing
                }
                print "\n";
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
    -a    : comma separated list of analyzers to be used. Defaut to all. 
    -d    : refresh dependent analyzers (default : no)
    -f    : output format : html
    -o    : folder for output : /tmp
    -p    : prefixe for the tables to be used. Default to 'tokens'

TEXT;
    
    die();
}

?>