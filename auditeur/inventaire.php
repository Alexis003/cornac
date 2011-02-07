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

include('../library/Cornac/Autoload.php');
spl_autoload_register('Cornac_Autoload::autoload');

include('../libs/getopts.php');
include('../libs/write_ini_file.php');
include('../libs/ods/ooo_ods.php');

// @todo use options from getopts library
$args = $argv;

$help = get_arg($args, '-?') ;
if ($help) { help(); }

$output_file = get_arg_value($args, '-o','inventory');
if (preg_match('#[^a-zA-Z0-9_/\.\-]#', $output_file)) {
    print "Invalid output file '$output_file'. Aborting\n";
    die();
}
if (strtolower(substr($output_file, 0, -4)) != '.ods') {
    $output_file .= ".ods";
}

if (file_exists($output_file)) {
    if (get_arg($args, '-k')) {
        unlink($output_file);
        print "Old file '$output_file' removed\n";
    } else {
        print "$output_file already exists. Use -k to force removal. Aborting\n";
        die();
    }
}

// @todo : check $format for values
// @todo : check output for being a folder

// @question : should options be constants?
// default values, stored in a INI file
$ini = get_arg_value($args, '-I', null);
if (!is_null($ini)) {
    global $INI;
    if (file_exists('../ini/'.$ini)) {
        define('INI','../ini/'.$ini);
    } elseif (file_exists('../ini/'.$ini.".ini")) {
        define('INI','../ini/'.$ini.".ini");
    } elseif (file_exists($ini)) {
        define('INI',$ini);
    } else {
        if (!file_exists('../ini/'.'tokenizeur.ini')) {
            die("No configuration file available ($ini nor tokenizeur.ini)\n");
        }
        define('INI','../ini/'.'tokenizeur.ini');
    }
    $INI = parse_ini_file(INI, true);
} else {
    define('INI',null);
    $INI = array("reader" => array( 'file' => '', ));
}

$INI['reader']['dependences'] = (bool) get_arg_value($args, '-d', false);
$INI['reader']['module'] = get_arg_value($args, '-a', @$INI['reader']['module']);
$INI['reader']['file']   = get_arg_value($args, '-f', ''  );
$INI['reader']['output'] = get_arg_value($args, '-o', @$INI['reader']['output']);
$INI['reader']['format'] = get_arg_value($args, '-F', @$INI['reader']['format']);

// validations
if (empty($INI['reader']['format'])) {
    print "Output format is needed (option -F) : xml or html\n";
    help();
}

// @todo put this into a central library
$DATABASE = new Cornac_Database();

$res = $DATABASE->query("SHOW TABLES LIKE '<report>'");
$row = $res->fetch();
if (!$row) {
    print "Auditeur haven't been run yet. Run it before inventaire.\n ./auditeur.php -I $ini -a Inventaire\n";
    die();
}

if (isset($INI['cornac']['prefix'])) {
    $prefix = $INI['cornac']['prefix'];
} else {
    $prefix = 'tokens';
}
unset($ini);

// @todo internationalize this!
// @todo make this configurable from the local web
$headers = array('Variables' => 'SELECT COUNT(DISTINCT element)  FROM <report> WHERE module="Variables_Names"',
                 'Files'  => 'SELECT COUNT(DISTINCT file) FROM <report>',
                 'Classes'   => 'SELECT COUNT(DISTINCT element)  FROM <report> WHERE module="Classes_Definitions"',
                 'Properties'   => 'SELECT COUNT(DISTINCT element) FROM <report> WHERE module="Classes_Properties"',
                 'Methods'   => 'SELECT COUNT(DISTINCT element)  FROM <report> WHERE module="Classes_MethodsDefinition"',
                 'Class Constants'   => 'SELECT COUNT(DISTINCT element) FROM <report> WHERE module="Classes_Constants"',
                 'Magic methods'   => 'SELECT IF(COUNT(DISTINCT element) > 0, "Yes","No")  FROM <report> WHERE module="Classes_MethodsSpecial"',
                 'Interfaces'   => 'SELECT COUNT(DISTINCT element)  FROM <report> WHERE module="Classes_Interfaces"',
                 'Namespaces'   => 'SELECT COUNT(DISTINCT element)  FROM <report> WHERE module="Php_Namespace"',
                 'Functions'   => 'SELECT COUNT(DISTINCT element)  FROM <report> WHERE module="Functions_Definitions"',
                 'Constants'   => 'SELECT COUNT(DISTINCT element) FROM <report> WHERE module="Constants_Definitions"',
                 'Fluent interfaces'   => 'SELECT IF(COUNT(DISTINCT element) > 0, "Yes","No")  FROM <report> WHERE module="Structures_FluentInterface"',
                 'References'   => 'SELECT IF(COUNT(DISTINCT element) > 0, "Yes","No")  FROM <report> WHERE module="Php_References"',
                 'Variable variables'   => 'SELECT IF(COUNT(DISTINCT element) > 0, "Yes","No")  FROM <report> WHERE module="Variables_Variables"',
                 'Ticks'   => 'SELECT IF(COUNT(*) > 0, "Yes","No")  FROM <tokens> WHERE type="_declare"',
                 'Uncompilable files'   => 'SELECT IF(COUNT(target) > 0, "Yes","No")  FROM <tasks> WHERE completed=3',
                 'External libraries'   => 'SELECT IF(COUNT(target) > 0, "Yes","No")  FROM <tasks> WHERE completed=3',
                 'Closures'   => 'SELECT IF(COUNT(file) > 0, "Yes","No")  FROM <tokens> WHERE type="_closure"',
                 'Uses Zend Framework'   => 'SELECT IF(COUNT(DISTINCT element) > 0, "Yes","No")  FROM <report> WHERE module="Zf_Dependencies"',
                 'Uses Symfony'   => 'SELECT IF(COUNT(DISTINCT element) > 0, "Yes","No")  FROM <report> WHERE module="Sf_Dependencies"',
                 'Have phpinfo'   => 'SELECT IF(COUNT(DISTINCT element) > 0, "Yes","No")  FROM <report> WHERE module="Php_Phpinfo"',
                 'Have debug'   => 'SELECT IF(COUNT(DISTINCT element) > 0, "Yes","No")  FROM <report> WHERE module="Ext_VarDump"',
                 );

$stats = array();

$ods = new ooo_ods();

$ods->setRow('Sommaire',1, array(1 => 'Module','Nombre'));
$ods->setRowCellsStyle('Sommaire', 1, "ce1");

$cell_row = 1;
foreach($headers as $name => $sql) {
    $cell_row++;
    $res = $DATABASE->query($sql);
    $row = $res->fetch();
    $ods->setRow('Sommaire',$cell_row, array(1 => $name, $row[0]));
}

// @attention : should also support _dot reports
$names = array("PHP extensions" => array('query' => 'SELECT DISTINCT element FROM <report> WHERE module="Php_Modules" ORDER BY element',
                                         'headers' => array('Extension'),
                                         'columns' => array('element')),
               "Constants" => array('query' => 'SELECT element, COUNT(*) as NB FROM <report> WHERE module="Constants_Definitions" GROUP BY element ORDER BY NB DESC',
                                    'headers' => array('Constant','Number'),
                                    'columns' => array('element','NB')),
               "Classes" => array('query' => 'SELECT T1.class, T1.file AS file, IFNULL(T2.code, "") AS abstract
FROM <tokens> T1
LEFT JOIN <tokens> T2
    ON T2.file = T1.file AND
       T2.code != T1.class AND
       (T2.left = T1.left + 1 ) AND
       T2.type = "token_traite"
WHERE T1.type="_class" AND
      T1.class!= "" 
ORDER BY T1.class',
                                  'headers' => array('Classe','abstract','File'),
                                  'columns' => array('class','file','abstract')),
               "Interfaces" => array('query' => 'SELECT element, COUNT(*) as NB FROM <report> WHERE module="Classes_Interfaces" GROUP BY element ORDER BY NB DESC',
                                    'headers' => array('Interface','Number'),
                                    'columns' => array('element','NB')),
               "Methods" => array('query' => 'SELECT T1.class, T1.scope AS method, T1.file AS file, 
if (SUM(if(T2.code="private",1,0))>0, "private","") AS private,
if (SUM(if(T2.code="protected",1,0))>0, "protected","") AS protected,
if ((SUM(if(T2.code="public",1,0))>0) OR 
(SUM(if(T2.code="protected",1,0)) + SUM(if(T2.code="private",1,0)) = 0), "public","") as public,
if (SUM(if(T2.code="abstract",1,0))>0, "abstract","") as abstract,
if (SUM(if(T2.code="final",1,0))>0, "final","") as final,
if (SUM(if(T2.code="static",1,0))>0, "static","") as static
FROM <tokens> T1
LEFT JOIN <tokens> T2
    ON T2.file = T1.file AND
       T2.type = "token_traite" AND
       (T2.left = T1.left + 1 OR 
        T2.left = T1.left + 3 OR 
        T2.left = T1.left + 5
        )
WHERE T1.type="_function" AND
      T1.class!= ""
GROUP BY T1.class, T1.scope, T1.file
ORDER BY T1.class, T1.scope
',
                                    'headers' => array('Class','Method','private','protected','public','static','final','abstract','File'),
                                    'columns' => array('class','method','private','protected','public','static','final','abstract','file')),
               "Properties" => array('query' => 'SELECT T1.class, T1.code AS property, T1.file AS file, 
if (SUM(if(T2.code="public",1,0))>0, "public","") as public,
if (SUM(if(T2.code="protected",1,0))>0, "protected","") as protected,
if (SUM(if(T2.code="private",1,0))>0, "private","") as private,
if (SUM(if(T2.code="static",1,0))>0, "static","") as static
FROM <tokens> T1
LEFT JOIN <tokens> T2
    ON T2.file = T1.file AND
       T2.code != T1.class AND
       (T2.left = T1.left + 1 OR
        T2.left = T1.left + 3 OR
        T2.left = T1.left + 5) AND
       T2.type = "token_traite"
WHERE T1.type="_var" AND
      T1.class!= ""
GROUP BY T1.class, T1.code
ORDER BY T1.class
',
                                    'headers' => array('Class','Property','private','protected','public','static','File'),
                                    'columns' => array('class','property','private','protected','public','static','file')),
               "Functions" => array('query' => 'SELECT element, file FROM <report> WHERE module="Functions_Definitions" ORDER BY element',
                                    'headers' => array('Functions','Number'),
                                    'columns' => array('element','file')),
               "Parameters" => array('query' => 'SELECT element, COUNT(*) as NB FROM <report> WHERE module="Variables_Gpc" GROUP BY element ORDER BY NB DESC',
                                    'headers' => array('Variable','Number'),
                                    'columns' => array('element','NB')),
               "Sessions" => array('query' => 'SELECT element, COUNT(*) as NB FROM <report> WHERE module="Variables_Session" GROUP BY element ORDER BY NB DESC',
                                    'headers' => array('Variable','Number'),
                                    'columns' => array('element','NB')),
               "Variables" => array('query' => 'SELECT element, COUNT(*) as NB FROM <report> WHERE module="Variables_Names" GROUP BY element ORDER BY NB DESC',
                                    'headers' => array('Variable','Number'),
                                    'columns' => array('element','NB')),
               "Globals"  => array(  'query' => 'SELECT element, COUNT(*) as NB FROM <report> WHERE module="Php_Globals" GROUP BY element ORDER BY NB DESC',
                                    'headers' => array('Variable','Number'),
                                    'columns' => array('element','NB')),
               "Files   " => array('query' => 'SELECT DISTINCT file FROM <report> GROUP BY file ORDER BY file DESC',
                                    'headers' => array('file'),
                                    'columns' => array('file')),
               "Can't compile" => array('query' => 'SELECT target FROM <tasks> WHERE completed = 3',
                                    'headers' => array('file'),
                                    'columns' => array('file')),
               "Extensions " => array('query' => 'SELECT RIGHT(file, LOCATE(".", REVERSE(file)) - 1) AS ext, COUNT(*) AS number
                                                    FROM (SELECT DISTINCT file FROM <tokens>)
                                                  files GROUP BY ext ORDER BY COUNT(*)',
                                    'headers' => array('ext','number'),
                                    'columns' => array('ext', 'number')),
);

foreach($names as $name => $conf) {
    extract($conf);

    $confs = array('query','headers','columns');
    foreach($confs as $conf) {
        if (!isset($$conf)) {
            print "Missing '$conf' info in configuration for '$name' : aborting\n";
            continue;
        }
    }

    $res = $DATABASE->query($query);
    $rows = $res->fetchAll(PDO::FETCH_ASSOC);

    foreach($headers as $id => $header) {
        $ods->setCell($name, 1, $id + 1, $header);
        $ods->setCellStyle($name, 1, $id + 1, "ce1");
    }

    foreach($columns as $id => $col) {
       $r = multi2array($rows, $col);
       $r[] = $r[0];
       unset($r[0]);
       $r[] = $r[1];
       unset($r[1]);
       $ods->setCol($name, $id + 1, $r);
    }
}

$filename = "./$output_file";

if ($ods->save($filename)) {
    print "Done\n";
} else {
    print "Failed\n";
}

?>