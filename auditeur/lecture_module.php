#!/usr/bin/php
<?php

define('OPT_NO_VALUE',false);
define('OPT_WITH_VALUE',true);

$args = $argv;

// @todo : use the getopt library
//$prefixe = getOption($args, '-I', OPT_WITH_VALUE, null);
$prefixe = getOption($args, '-p', OPT_WITH_VALUE, 'tokens');
$module = getOption($args, '-a', OPT_WITH_VALUE,  null);
$fichier = getOption($args, '-f', OPT_WITH_VALUE, null);
$output = getOption($args, '-o', OPT_WITH_VALUE, null);
$format = getOption($args, '-F', OPT_WITH_VALUE, 'xml');
$summary = getOption($args, '-s', OPT_NO_VALUE, null);
$help = getOption($args, '-h', OPT_NO_VALUE, null);

if ($help) { help(); }

// @todo : check $format for values
// @todo : check output for being a folder

// @question : should options be constants?

define('VERBOSE', getOption($args, '-v', null, OPT_NO_VALUE));

if (VERBOSE) {
    print "Working on tables prefixed with $prefixe\n";
    print "Working on module $module\n";
    if (!empty($fichier)) {
        print "Working with file $fichier\n";
    } else {
        print "Working on all files\n";
    }
    print "Saving to $output\n";
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
if (VERBOSE) {  
    print "Fichier de directives : ".INI."\n";
}
global $INI;
$INI = parse_ini_file(INI, true);

if (isset($INI['mysql']) && $INI['mysql']['active'] == true) {
    $database = new pdo($INI['mysql']['dsn'],$INI['mysql']['username'], $INI['mysql']['password']);
} elseif (isset($INI['sqlite'])  && $INI['sqlite']['active'] == true) {
    $database = new pdo($INI['sqlite']['dsn']);
} else {
    print "No database configuration provided (no mysql, no sqlite)\n";
    die();
}


// @attention : should also support _dot reports
$requete = 'SELECT * FROM '.$prefixe.'_rapport WHERE module='.$database->quote($module);
if (!empty($fichier)) {
    $requete .= ' AND fichier='.$database->quote($fichier);
}
$res = $database->query($requete);
//print_r($database->errorInfo());

// @attention : should support -s for summaries. 

include('render/'.$format.'.php');
$class = "Render_".$format;

$view = new $class($database, $prefixe, $fichier);

if (get_class($view) == 'Render_html') {
    $view->SetFolder($output);
}

if (!$res) {
    $result = $view->render(array());    
} else {
    $rows = $res->fetchall(PDO::FETCH_ASSOC);
    $result = $view->render($rows);
}

if (empty($output)) {
    print $result; 
} else {
//    $size = file_put_contents($output, $result); 
    print "File written in '$output'\n";
}

function getOption(&$args, $option, $value = true, $default = null) {
    if ($id = array_search($option, $args)) {
        unset($args[$id]);
        
        if ($value) {
            $prefixe = $args[$id + 1];
            unset($args[$id + 1]);
        } else {
            $prefixe = true;
        }
    } else {
        if ($value) {
            $prefixe = $default;
        } else {
            $prefixe = false;
        }
    }
    
    return $prefixe;
}

function help() {
    print <<<SHELL
Usage : ./lecture_module.php

Options : 
-a     : analyzer to report
-I     : .INI file of configuration
-f     : report is limited to this file
-F     : format to use. Supported : xml (default)
-s     : produce the current summary of available analyzer
-h     : This help
-o     : folder of output

SHELL;
    die();
}

?>