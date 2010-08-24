#!/usr/bin/php
<?php

include('../libs/getopts.php');
include('../libs/write_ini_file.php');

$args = $argv;

$help = get_arg($args, '-?') ;
if ($help) { help(); }

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

if (isset($INI['mysql']) && $INI['mysql']['active'] == true) {
    $database = new pdo($INI['mysql']['dsn'],$INI['mysql']['username'], $INI['mysql']['password']);
//    print "MySQL\n";
} elseif (isset($INI['sqlite'])  && $INI['sqlite']['active'] == true) {
    $database = new pdo($INI['sqlite']['dsn']);
//    print "sqlite\n";
} else {
    print "No database configuration provided (no mysql, no sqlite)\n";
    die();
}

if (isset($INI['cornac']['prefix'])) {
    $prefix = $INI['cornac']['prefix'];
} else {
    $prefix = 'tokens';
}

write_ini_file($INI, INI);

// @attention : should also support _dot reports
$requete = 'SELECT * FROM '.$prefix.'_rapport WHERE module='.$database->quote($INI['reader']['module']);
if (!empty($INI['reader']['file'])) {
    $requete .= ' AND fichier='.$database->quote($INI['reader']['file']);
}
$res = $database->query($requete);

// @attention : should support -s for summaries. 

include('render/'.$INI['reader']['format'].'.php');
$class = "Render_".$INI['reader']['format'];

$view = new $class($database, $prefix, $INI['reader']['file']);

// @bug : shouldn't be here
if (get_class($view) == 'Render_html') {
    $view->SetFolder($INI['reader']['output']);
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
    print "File written in '$output'\n";
}

function help() {
    print <<<SHELL
Usage : ./reader.php

Options : 
-?     : this help
-a     : analyzer to report
-I     : .INI file of configuration
-f     : report is limited to this file
-F     : format to use. Supported : xml (default)
-s     : produce the current summary of available analyzer
-p     : application
-o     : folder of output

SHELL;
    die();
}

?>