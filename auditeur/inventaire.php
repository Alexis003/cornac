#!/usr/bin/php
<?php

include('../libs/getopts.php');
include('../libs/write_ini_file.php');
include('../libs/ods/ooo_ods.php');

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
    print "$output_file already exists. Aborting\n";
    die();
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
    $INI = array("reader" => array( 'fichier' => '', ));
}
unset($ini);

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


// @todo : support later
//$summary = getOption($args, '-s', OPT_NO_VALUE, null);

// @todo put this into a central library 
include('../libs/database.php');
$DATABASE = new database();

if (isset($INI['cornac']['prefix'])) {
    $prefix = $INI['cornac']['prefix'];
} else {
    $prefix = 'tokens';
}

// @todo internationalize this!
$headers = array('Variables' => 'SELECT COUNT(DISTINCT element) FROM <rapport> WHERE module="variables"',
                 'Fichiers'  => 'SELECT COUNT(DISTINCT fichier) FROM <rapport>',
                 'Classes'   => 'SELECT COUNT(DISTINCT element) FROM <rapport> WHERE module="classes"',
                 'Constantes'   => 'SELECT COUNT(DISTINCT element) FROM <rapport> WHERE module="defconstantes"',
                 'Utilise Zend Framework'   => 'SELECT COUNT(DISTINCT element) FROM <rapport> WHERE module="zfClasses"',
                 'Interfaces'   => 'SELECT COUNT(DISTINCT element) FROM <rapport> WHERE module="interfaces"',
                 'Interfaces fluides'   => 'SELECT COUNT(DISTINCT element) FROM <rapport> WHERE module="fluid_interface"',
                 'References'   => 'SELECT COUNT(DISTINCT element) FROM <rapport> WHERE module="references"',
                 'Variables variables'   => 'SELECT COUNT(DISTINCT element) FROM <rapport> WHERE module="variablesvariables"',
                 'Constantes de classes'   => 'SELECT COUNT(DISTINCT element) FROM <rapport> WHERE module="constantes_classes"',
                 );

$stats = array();

$ods = new ooo_ods();

$ods->setRow('Sommaire',1, array(1 => 'Module','Nombre'));

$cell_row = 1;
foreach($headers as $name => $sql) {
    $cell_row++;
    $res = $DATABASE->query($sql);
    $row = $res->fetch();
    $ods->setRow('Sommaire',$cell_row, array(1 => $name, $row[0]));
}

// @attention : should also support _dot reports
$names = array("Modules PHP" => array('query' => 'SELECT DISTINCT element FROM <rapport> WHERE module="php_modules" ORDER BY element',
                                  'headers' => array('Extension'),
                                  'columns' => array('element')),
               "Constantes" => array('query' => 'SELECT element, COUNT(*) as NB FROM <rapport> WHERE module="defconstantes" GROUP BY element ORDER BY NB DESC',
                                    'headers' => array('Constant','Number'),
                                    'columns' => array('element','NB')),
               "Classes" => array('query' => 'SELECT element, fichier FROM <rapport> WHERE module="classes" ORDER BY element',
                                  'headers' => array('Classe','File'),
                                  'columns' => array('element','fichier')),
               "Interfaces" => array('query' => 'SELECT element, COUNT(*) as NB FROM <rapport> WHERE module="interface" GROUP BY element ORDER BY NB DESC',
                                    'headers' => array('Interface','Number'),
                                    'columns' => array('element','NB')),
               "Méthodes" => array('query' => 'SELECT DISTINCT class, scope AS method, fichier FROM '.$prefix.' WHERE class != "" AND scope != "global" ORDER BY class, scope',
                                    'headers' => array('Class','Method','File'),
                                    'columns' => array('class','method','fichier')),
               "Fonctions" => array('query' => 'SELECT element, fichier FROM <rapport> WHERE module="deffunctions" ORDER BY element',
                                    'headers' => array('Functions','Number'),
                                    'columns' => array('element','fichier')),
               "Paramètres" => array('query' => 'SELECT element, COUNT(*) as NB FROM <rapport> WHERE module="gpc_variables" GROUP BY element ORDER BY NB DESC',
                                    'headers' => array('Variable','Number'),
                                    'columns' => array('element','NB')),
               "Sessions" => array('query' => 'SELECT element, COUNT(*) as NB FROM <rapport> WHERE module="session_variables" GROUP BY element ORDER BY NB DESC',
                                    'headers' => array('Variable','Number'),
                                    'columns' => array('element','NB')),
               "Variables" => array('query' => 'SELECT element, COUNT(*) as NB FROM <rapport> WHERE module="variables" GROUP BY element ORDER BY NB DESC',
                                    'headers' => array('Variable','Number'),
                                    'columns' => array('element','NB')),
               "Fichiers" => array('query' => 'SELECT DISTINCT fichier FROM <rapport> GROUP BY fichier ORDER BY fichier DESC',
                                    'headers' => array('Fichier'),
                                    'columns' => array('fichier')),
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
    
    foreach($headers as $id => $header) {
        $ods->cells[$name][1][$id + 1] = $header;

        $res = $DATABASE->query($query);
        $rows = $res->fetchAll(PDO::FETCH_ASSOC);
    
        foreach($columns as $id => $col) {
            $r = multi2array($rows, $col);

            $ods->setCol($name, $id + 1, $r);
        }
    }
}

$filename = "./$output_file";

if ($ods->save($filename)) {
    print "Done\n";
} else {
    print "Failed\n";
}

?>