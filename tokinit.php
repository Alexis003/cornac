#!/usr/bin/php 
<?php
// @fer -d xdebug.profiler_enable=On

ini_set('memory_limit',234217728);

$times = array('debut' => microtime(true));
include('prepare/commun.php');
include('libs/tok.php');
include("prepare/analyseur.php");

// @doc Reading the name of the processed application

global $FIN; 
// Collecting tokens
$FIN['debut'] = microtime(true);

include('libs/getopts.php');

// @todo : exporter les informations d'options dans une inclusion
$args = $argv;
$help = get_arg($args, '-?') ;
if ($help) { help(); }

// @doc default values, stored in a INI file
$ini = get_arg_value($args, '-I', null);
if (!is_null($ini)) {
    global $INI;
    if (file_exists('ini/'.$ini)) {
        define('INI','ini/'.$ini);
    } elseif (file_exists('ini/'.$ini.".ini")) {
        define('INI','ini/'.$ini.".ini");
    } elseif (file_exists($ini)) {
        define('INI',$ini);
    } else {
        define('INI','ini/'.'cornac.ini');
    }
    $INI = parse_ini_file(INI, true);
} else {
    define('INI',null);
    $INI = array();
}
unset($ini);
// @todo : what happens if we can't find the .INI ?
print "Directives files : ".INI."\n";

// @doc Reading constantes that are in the .INI
define('TOKENS',(bool) get_arg($args, '-t'));
define('TEST'  ,(bool) get_arg($args, '-T'));
define('STATS' ,(bool) get_arg($args, '-S', false));
define('VERBOSE', (bool) get_arg($args, '-v'));

if ($templates = get_arg_value($args, '-g', null)) {
    $templates = explode(',', $templates);

    $templates = array_unique($templates);
    
    foreach ($templates as $i => $template) {
        if (!file_exists('prepare/templates/template.'.$template.'.php')) {
            print "$id) '$template' doesn't exist. Ignoring\n";
            unset($templates[$i]);
        } else {
            print "Using template ".$template."\n";
        }
    }
    
    if (count($templates) == 0) {
        $templates = array('tree');
    }
    
    
    define('GABARIT',join(',',$templates));
} else {
    define('GABARIT','tree');
    $templates = array('tree');
}

include('./libs/database.php');

define('LOG' ,(bool) get_arg($args, '-l', false));
$limite = 0 + get_arg_value($args, '-i', 0);
if ($limite) {
    print "Cycles = $limite\n";
} else {
    $limite = -1;
}

define('RECURSIVE' ,(bool) get_arg($args, '-r', false));

$dossier = get_arg_value($args, '-d', array());
if (!empty($dossier)) {
    if (substr($dossier, -1) == '/') {
        $dossier = substr($dossier, 0, -1);
    }

    if (!file_exists($dossier)) {
        print "Couldn't find folder '$dossier'\n Aborting\n";
        die();
    }

    print "Preparing work on folder '{$dossier}'\n";
    
    $files = glob($dossier.'/*.php');
    
    foreach($files as $file) {
        $query = "INSERT INTO tu_tasks VALUES (NULL, 'tokenize', ".$database->quote($file).", ".$database->quote(GABARIT).", NOW(), 0)";
        $database->query($query);
    }
    
    if (RECURSIVE) {
        $files = liste_directories_recursive($dossier);
        print "Preparing recursive work on folder {$dossier}\n";

        foreach($files as $file) {
            $code = file_get_contents($file);
            if (strpos($code, '<?') === false) { continue; }
            
            $query = "INSERT IGNORE INTO tu_tasks VALUES (NULL, 'tokenize', ".$database->quote($file).", ".$database->quote(GABARIT).",NOW(), 0)";
            $database->query($query);
        }
    }
} elseif ($file = get_arg_value($args, '-f', '')) {
    print "Working on file '{$file}'\n";

    $query = "INSERT IGNORE INTO tu_tasks VALUES (NULL, 'tokenize', ".$database->quote($file).", ".$database->quote(GABARIT).", NOW(), 0)";
    $database->query($query);
} else {
    print "No files to work on\n";
    help();
}

print "Done\n";


?>