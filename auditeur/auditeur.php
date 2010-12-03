#!/usr/bin/env php
<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 Alter Way Solutions (France)                      |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Author: Damien Seguy <damien.seguy@gmail.com>                        |
   +----------------------------------------------------------------------+
 */

include('../libs/write_ini_file.php');

// @synopsis : read options
$options = array('help' => array('help' => 'display this help',
                                 'option' => '?',
                                 'compulsory' => false),
                 'ini' => array('help' => 'configuration set or file',
                                 'get_arg_value' => null,
                                 'option' => 'I',
                                 'compulsory' => true),
                 'clean' => array('help' => 'clean database',
                                 'option' => 'K',
                                 'compulsory' => false),
                 'init' => array('help' => 'init auditeur, don\'t run it',
                                 'get_arg_value' => 1,
                                 'option' => 'i',
                                 'compulsory' => false),
                 'slave' => array('help' => 'slave mode. -1, infinite; 0, all task available; n : number of tasks',
                                 'get_arg_value' => 0,
                                 'option' => 's',
                                 'compulsory' => false),
                 'analyzers' => array('help' => 'analyzers applied (default = all)',
                                      'get_arg_value' => 'all',
                                      'option' => 'a',
                                      'compulsory' => false),
                 'dependences' => array('help' => 'force update dependences',
                                        'option' => 'd',
                                        'compulsory' => false),
                 'directory' => array('help' => 'directory to work in',
                                      'get_arg_value' => null,
                                      'option' => 'd',
                                      'compulsory' => false),
                 );
include('../libs/getopts.php');

define('CLEAN_DATABASE', !empty($INI['clean']));

$modules = array(
'_new',
'affectations_variables',
//'appelsfonctions', // @_
'arobases',
'constantes',
'constantes_classes',
'defconstantes',
'deffunctions',
'doubledeffunctions',
'doubledefclass',
'defmethodes',
'dieexit',
//'dir_functions' @_, 
'Ext_Dir',
'emptyfunctions',
'error_functions',
'evals',
'execs',
'functions_frequency',
'functions_unused',
'functionscalls',
'globals',
'gpc',
'headers',
'iffectations',
'ifsanselse',
//'image_functions' @_, 
'Ext_Image',
'inclusions',
'inclusions_path',
'inclusions2',
//'ldap_functions' @_, 
'Ext_Ldap',
'literals',
'method_special',
'methodscall',
'multi_def_files',
//'mssql_functions' @_, 
'Ext_Mssql',
'nestedif',
'nestedloops',
'parentheses',
'php_functions',
'php_modules',
'proprietes_publiques',
'regex',
'returns',
'secu_protection_functions',
//'session_functions' @_, 
'Ext_Session',
'sql_queries',
'statiques',
'tableaux',
'tableaux_gpc',
'tableaux_gpc_seuls',
'thrown',
'undeffunctions',
'unused_args',
'vardump',
'variables',
'variablesvariables',
//'xdebug_functions' @_, 
'Ext_Xdebug',
//'xml_functions' @_, 
'Ext_Xml',
'properties_defined',
'properties_used',
'html_tags',
//'affectations_gpc', @_
//'classes_nb_methods', @_
'unused_properties',
'undefined_properties',
'indenting',
'block_of_call',
'variables_relations',
'arglist_def',
'arglist_call',
'arglist_disc',
'variables_one_letter',
'variables_lots_of_letter',
'php_classes',
//'upload_functions' @_, 
'Ext_Upload',
'affectations_direct_gpc',
'affectations_literals',
'concatenation_gpc',
'variables_unaffected',
'gpc_affectations',
'dangerous_combinaisons',
'literals_reused',
'literals_long',
'interfaces',
'functions_without_returns',
'session_variables',
'gpc_variables',
'mvc',
'globals_link',
'defarray',
'multidimarray',
'popular_libraries',
'addElement',
'addElement_unaffected',
'constantes_link',
'function_link',
'foreach_unused',
'_this',
'references',
'keyval',
'keyval_outside',
'return_with_dead_code',
'functions_lines',
'callback_functions',
'functions_with_callback',
'fluid_interface',
'handlers',
'php_functions_name_conflict',
'php_constant_name_conflict',
'php_classes_name_conflict',
'abstracts',
'finals',
'function_args_reference',
'php_keywords',
//'literals_as_argref', @todo support ogher PHP version for token_get_all before activating
//'tostring_no_arg',  @todo support ogher PHP version for token_get_all before activating
'function_return_unused',
'long_loops',
'method_without_ppp',
'loops_oneliner',
'pearClasses',
'recursive',
'rawtext_whitespace',
'loaded_lines',
'infinite_loop',
'comparison_constant',
'random_functions',
'if_no_comparison',
'case_without_break',
'switch_without_default',

'Zf',
'Zf_Action',
'Zf_Classes',
'Zf_Controller',
'Zf_Db',
'Zf_Dependencies',
'Zf_Elements',
'Zf_GetGPC',
'Zf_Redirect',
'Zf_SQL',
'Zf_Session',
'Zf_TypeView',
'Zf_ViewVariables',

'Classes',
'Classes_Hierarchy',
'Classes_MethodsCount',
'Classes_Undefined',
'Classes_Unused',
'Classes_Used',

'Ext',
'Ext_Mysql',
'Ext_File',
'Ext_Ereg',
'Ext_Filter',
'Ext_Mysqli',

// new analyzers
);

include('../libs/database.php');
$DATABASE = new database();

if ($INI['init']) {
    if ($INI['analyzers'] == 'all' ) {
     // default : all modules
    } else {
        $m = explode(',', $INI['analyzers']);

        $diff = array_diff($m , $modules);
        if (count($diff) > 0) {
            print count($diff)." analyzers are unknown and omitted : ".join(', ', $diff)."\n";
        }

        $m = array_intersect($m, $modules);
        if (count($m) == 0) {
            print "No analyzer provided : Aborting\n";
            die();
        } else {
            $modules = $m;
        }
    }
    
    $query = "DELETE FROM <tasks> WHERE task='auditeur' AND target IN ('".join("', '", $modules)."')";
    $DATABASE->query($query);
    
    $query = "INSERT INTO <tasks> (task, target, date_update, completed) VALUES ( 'auditeur', '".join("',NOW(), 0) ,('auditeur', '", $modules)."', NOW(), 0)";
    $DATABASE->query($query);
    
    print count($modules)." modules will be treated : ".join(', ', $modules)."\n";
// @todo fix the problem with the path
/*
if (INI) {
    write_ini_file($INI, INI);
*/

if (isset($INI['mysql']) && $INI['mysql']['active'] == true) {
// @note element column size should match the code column in <tokens>
    if (CLEAN_DATABASE) {
        $DATABASE->query('DROP TABLE <rapport>');
    }
    $DATABASE->query('CREATE TABLE IF NOT EXISTS <rapport> (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file` varchar(500) NOT NULL,
  `element` varchar(10000) NOT NULL,
  `token_id` int(10) unsigned NOT NULL,
  `module` varchar(50) NOT NULL,
  `checked` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `element` (`element`),
  KEY `file` (`file`),
  KEY `module` (`module`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1');

    if (CLEAN_DATABASE) {
        $DATABASE->query('DROP TABLE <rapport_dot>');
    }
        $DATABASE->query('CREATE TABLE IF NOT EXISTS <rapport_dot> (
  `a` varchar(255) NOT NULL,
  `b` varchar(255) NOT NULL,
  `cluster` varchar(255) NOT NULL DEFAULT \'\',
  `module` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1');

    if (CLEAN_DATABASE) {
        $DATABASE->query('DROP TABLE <rapport_module>');
    }
        $DATABASE->query('CREATE TABLE IF NOT EXISTS <rapport_module> (
  `module` varchar(255) NOT NULL,
  `fait` datetime NOT NULL,
  `format` enum("html","dot","gefx") NOT NULL,
  `web` ENUM("yes","no") DEFAULT "yes",
  PRIMARY KEY (`module`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1');

} elseif (isset($INI['sqlite'])  && $INI['sqlite']['active'] == true) {
// @todo : support drop of table with option -K
// @code $database->query('DELETE FROM '.$INI['cornac']['prefix'].'_rapport WHERE file = "'.$file.'"');
    $DATABASE->query('CREATE TABLE IF NOT EXISTS <rapport>
  (id       INTEGER PRIMARY KEY   AUTOINCREMENT  ,
  `file` varchar(500) NOT NULL,
  `element` varchar(10000) NOT NULL,
  `token_id` int unsigned NOT NULL,
  `module` varchar(50) NOT NULL
)');

//    $DATABASE->query('DELETE FROM '.$INI['cornac']['prefix'].'_rapport_dot WHERE cluster = "'.$file.'"');
    $DATABASE->query('CREATE TABLE IF NOT EXISTS <rapport_dot> (
  `a` varchar(255) NOT NULL,
  `b` varchar(255) NOT NULL,
  `cluster` varchar(255) NOT NULL DEFAULT \'\',
  `module` varchar(255) NOT NULL
)');

    $DATABASE->query('CREATE TABLE IF NOT EXISTS <rapport_module> (
  `module` varchar(255) NOT NULL PRIMARY KEY,
  `fait` datetime NOT NULL,
  `format` varchar(255) NOT NULL,
  `web` ENUM("yes","no") DEFAULT 1

)');
} else {
    print "No database configuration provided (no mysql, no sqlite)\n";
    die();
}
}

// validation done


// rendu (templates) @_
include 'classes/sommaire.php';
$sommaire = new sommaire();

// @inclusions abstract classes
include 'classes/abstract/modules.php';
include 'classes/abstract/functioncalls.php';
include 'classes/abstract/typecalls.php';
include 'classes/abstract/noms.php';

//$modules_faits = array();

// @todo the init could take into account the current content of the database, avoiding reprocess

$counter = 0;
while (1) {
    foreach($modules as $module) {
        print "+ $module\n";
        analyse_module($module);
        $counter++;
        
        if ($INI['slave'] > 0 && $counter >= $INI['slave']) {
            print "$counter analyzer processed. Terminating.\n";
            die();
        }
    }

    if ($INI['slave'] == 0) {
        print "all analyzers tasks processed. Terminating.\n";
        die();
    }
    
    sleep(5);
    print "Processed $counter tasks. Waiting for 5s\n";
}

function __autoload($classname) {
    $path = str_replace('_','/', $classname);

    if (file_exists('classes/'.$path.'.php')) {
        require_once('classes/'.$path.'.php');
    } elseif (file_exists('classes/'.$classname.'.php')) {
        require_once('classes/'.$classname.'.php');
    }
}

function analyse_module($module_name) {
    global  $DATABASE, $sommaire, $INI;

    $res = $DATABASE->query("SELECT completed FROM <tasks> WHERE target='$module_name'");
    $row = $res->fetch(PDO::FETCH_ASSOC);
    if ($row['completed'] == 100) {
        return ;
    }

    $res = $DATABASE->query("SELECT AVG(completed) / 100 AS completed FROM <tasks> WHERE task='tokenize'");
    $row = $res->fetch(PDO::FETCH_ASSOC);
    $completed = $row['completed'];

    $module = new $module_name($DATABASE);
    $dependances = $module->dependsOn();

    if (count($dependances) > 0) {
        $res = $DATABASE->query("SELECT target FROM <tasks> WHERE target IN ('".join("','", $dependances)."') AND completed != 100");
        $manque = $res->fetchAll(PDO::FETCH_ASSOC);
        $manque = multi2array($manque, 'target');
        if (count($manque) > 0) {
            foreach($manque as $m) {
                print "  +  $m";
                if ($INI['dependences']) {
                    analyse_module($m);
                } else {
                    $res = $DATABASE->query('SELECT * FROM <rapport_module> WHERE module="'.$m.'"');
                    $row = $res->fetch();
                    if (!isset($row['module'])) {
                        analyse_module($m);
                        print " done ";
                    } else {
                        print " omitted ";
                    }
                }
                print "\n";
            }
        } else {
            print "Dépendances already processed\n";
        }
    }

    $init_time = microtime(true);
    if (isset($INI['auditeur.'.$module_name])) {
        $module->init($INI['auditeur.'.$module_name]);
    }
    $module->analyse();
    // @todo add an option for this
    $finish_time = microtime(true);
    $fp = fopen('auditeur.log','a');
    fwrite($fp, date('r')."\t$module_name\t{$INI['ini']}\t".number_format(($finish_time - $init_time) * 1000, 2,',','')."\r");
    fclose($fp);

    $module->sauve();

    $sommaire->add($module);
    $res = $DATABASE->query("UPDATE <tasks> SET completed = $completed WHERE target = '$module_name'");
}

$sommaire->sauve();
?>