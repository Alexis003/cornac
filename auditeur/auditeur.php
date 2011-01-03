#!/usr/bin/env php
<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 - 2011 Alter Way Solutions (France)               |
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

'Classes',
'Classes_Abstracts',
'Classes_Definitions',
'Classes_DoubleDeclaration',
'Classes_Finals',
'Classes_Hierarchy',
'Classes_Interfaces',
'Classes_MethodsCount',
'Classes_MethodsDefinition',
'Classes_MethodsSpecial',
'Classes_MethodsWithoutPpp',
'Classes_News',
'Classes_Php',
'Classes_Properties',
'Classes_PropertiesPublic',
'Classes_PropertiesUndefined',
'Classes_PropertiesUnused',
'Classes_PropertiesUsed',
'Classes_Statics',
'Classes_This',
'Classes_ToStringNoArg', 
'Classes_Undefined',
'Classes_Unused',

'Constants',
'Constants_Definitions',
'Constants_FileLink',
'Constants_Usage',

'Commands',
'Commands_Html',
'Commands_Sql',

'Ext',
'Ext_CallingBack',
'Ext_DieExit',
'Ext_Dir',
'Ext_Ereg',
'Ext_Errors',
'Ext_Evals',
'Ext_Execs',
'Ext_File',
'Ext_Filter',
'Ext_Headers',
'Ext_Image',
'Ext_Ldap',
'Ext_Mssql',
'Ext_Mysql',
'Ext_Mysqli',
'Ext_Random',
'Ext_Session',
'Ext_Upload',
'Ext_VarDump',
'Ext_Xdebug',
'Ext_Xml',

'Functions', 
'Functions_ArglistCalled',
'Functions_ArglistDefined',
'Functions_ArglistDiscrepencies',
'Functions_ArglistReferences',
'Functions_ArglistUnused',
'Functions_ArrayUsage',
'Functions_CalledBack',
'Functions_CodeAfterReturn',
'Functions_Definitions',
'Functions_DoubleDeclaration',
'Functions_Emptys',
'Functions_FileLinks',
'Functions_Handlers',
'Functions_Inclusions',
'Functions_LinesCount',
'Functions_Occurrences',
'Functions_Php',
'Functions_Recursive',
'Functions_Security',
'Functions_Undefined',
'Functions_Unused',
'Functions_UnusedReturn',
//'functions_with_callback',
'Functions_WithoutReturns',

'Literals', 
'Literals_Definitions',
'Literals_InArglist', 
'Literals_Long',
'Literals_RawtextWhitespace',
'Literals_Reused',

'Php',
'Php_Arobases',
'Php_ArrayDefinitions',
'Php_ArrayMultiDim',
'Php_ClassesConflict',
'Php_ConstantConflict',
'Php_FunctionsCalls',
'Php_FunctionsConflict',
'Php_Globals',
'Php_GlobalsLinks',
'Php_InclusionLinks',
'Php_InclusionPath',
'Php_Keywords',
'Php_Modules',
'Php_References',
'Php_RegexStrings',
'Php_Returns',
'Php_Throws',

'Quality',
'Quality_DangerousCombinaisons',
'Quality_ExternalLibraries',
'Quality_FilesMultipleDefinition',
'Quality_GpcAssigned', 
'Quality_GpcConcatenation',
'Quality_GpcModified',
'Quality_GpcUsage',
'Quality_Indenting',
'Quality_Mvc',

'Structures',
'Structures_AffectationLiterals',
'Structures_AffectationsVariables',
'Structures_BlockOfCalls',
'Structures_CaseWithoutBreak',
'Structures_ComparisonConstants',
'Structures_FluentInterface',
'Structures_ForeachKeyValue',
'Structures_ForeachKeyValueOutside',
'Structures_ForeachUnused',
'Structures_FunctionsCallsLink',
'Structures_Iffectations',
'Structures_IfNested',
'Structures_IfWithoutComparison',
'Structures_IfWithoutElse',
'Structures_LinesLoaded',
'Structures_LoopsInfinite',
'Structures_LoopsLong',
'Structures_LoopsNested',
'Structures_LoopsOneLiner',
'Structures_MethodsCalls',
'Structures_Parenthesis',
'Structures_SwitchWithoutDefault',

'Test',

'Variables',
'Variables_Gpc',
'Variables_LongNames',
'Variables_Names',
'Variables_OneLetter',
'variables_Relations',
'Variables_Session',
'Variables_Unaffected',
'Variables_Variables',
'Variables_Affected',

'Zf',
'Zf_Action',
'Zf_AddElement',
'Zf_AddElementUnaffected',
'Zf_Classes',
'Zf_Controller',
'Zf_Db',
'Zf_Dependencies',
'Zf_Elements',
'Zf_GetGPC',
'Zf_Redirect',
'Zf_Session',
'Zf_SQL',
'Zf_TypeView',
'Zf_ViewVariables',

'Structures_FunctionsCalls',
'Classes_PropertiesChained',
'Php_Namespace',

'Sf',
'Sf_Dependencies',

'Inventaire',

'Pear',
'Pear_Dependencies',
'Quality_ClassesNotInSameFile',
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
    
// @todo fix the problem with the path
/*
if (INI) {
    write_ini_file($INI, INI);
*/

if (isset($INI['mysql']) && $INI['mysql']['active'] == true) {
// @note element column size should match the code column in <tokens>
    if (CLEAN_DATABASE) {
        $DATABASE->query('DROP TABLE IF EXISTS <report>');
        $DATABASE->query('DROP TABLE IF EXISTS <report_dot>');
        $DATABASE->query('DROP TABLE IF EXISTS <report_module>');
        print "3 tables cleaned\n";
        die();
    }
    $DATABASE->query('CREATE TABLE IF NOT EXISTS <report> (
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

        $DATABASE->query('CREATE TABLE IF NOT EXISTS <report_dot> (
  `a` varchar(255) NOT NULL,
  `b` varchar(255) NOT NULL,
  `cluster` varchar(255) NOT NULL DEFAULT \'\',
  `module` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1');

        $DATABASE->query('CREATE TABLE IF NOT EXISTS <report_module> (
  `module` varchar(255) NOT NULL,
  `fait` datetime NOT NULL,
  `format` enum("html","dot","gefx") NOT NULL,
  `web` ENUM("yes","no") DEFAULT "yes",
  PRIMARY KEY (`module`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1');

} elseif (isset($INI['sqlite'])  && $INI['sqlite']['active'] == true) {
// @todo : support drop of table with option -K
// @code $database->query('DELETE FROM '.$INI['cornac']['prefix'].'_report WHERE file = "'.$file.'"');
    $DATABASE->query('CREATE TABLE IF NOT EXISTS <report>
  (id       INTEGER PRIMARY KEY   AUTOINCREMENT  ,
  `file` varchar(500) NOT NULL,
  `element` varchar(10000) NOT NULL,
  `token_id` int unsigned NOT NULL,
  `module` varchar(50) NOT NULL
)');

//    $DATABASE->query('DELETE FROM '.$INI['cornac']['prefix'].'_report_dot WHERE cluster = "'.$file.'"');
    $DATABASE->query('CREATE TABLE IF NOT EXISTS <report_dot> (
  `a` varchar(255) NOT NULL,
  `b` varchar(255) NOT NULL,
  `cluster` varchar(255) NOT NULL DEFAULT \'\',
  `module` varchar(255) NOT NULL
)');

    $DATABASE->query('CREATE TABLE IF NOT EXISTS <report_module> (
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

    print count($modules)." modules will be treated : ".join(', ', $modules)."\n";

// validation done


// rendu (templates) @_
include 'classes/sommaire.php';
$sommaire = new sommaire();

// @inclusions abstract classes
include 'classes/abstract/modules.php';
include 'classes/abstract/modules_classe_dependances.php';
include 'classes/abstract/modules_head.php';
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

    $res = $DATABASE->query('SELECT * FROM <report_module> WHERE module="'.$m.'"');
    $row = $res->fetch();
    if (isset($row['module'])) {
        print "$out omitted (already in base) \n";
        return true;
    }

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
        $res = $DATABASE->query("SELECT target FROM <tasks> WHERE completed = 100 AND task='auditeur'");
        $done = $res->fetchAll(PDO::FETCH_ASSOC);
        $done = multi2array($done, 'target');
        
        $manque = array_diff($dependances, $done);
        
        if (count($manque) > 0) {
            foreach($manque as $m) {
                $out = "  +  $m ";
                if ($INI['dependences']) {
                    analyse_module($m);
                } else {
                    $res = $DATABASE->query('SELECT * FROM <report_module> WHERE module="'.$m.'"');
                    $row = $res->fetch();
                    if (isset($row['module'])) {
                        print "$out omitted (already in base) \n";
                    } else {
                        analyse_module($m);
                        print "$out done \n";
                    }
                }
            }
        } else {
            print "Dependencies already processed\n";
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