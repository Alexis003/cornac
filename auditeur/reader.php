#!/usr/bin/php
<?php

include('../libs/write_ini_file.php');

// @synopsis : read options
$options = array('help' => array('help' => 'display this help',
                                 'option' => '?',
                                 'compulsory' => false),
                 'ini' => array('help' => 'configuration set or file',
                                 'get_arg_value' => null,
                                 'option' => 'I',
                                 'compulsory' => true),
                 'analyzer' => array('help' => 'analyzer',
                                     'get_arg_value' => 'all',
                                     'option' => 'a',
                                     'compulsory' => false),
// @todo limit to one folder? 
                 'file' => array('help' => 'limit report to this file',
                                      'get_arg_value' => '',
                                      'option' => 'f',
                                      'compulsory' => false),
                 'output' => array('help' => 'output folder (will be created anyway)',
                                             'get_arg_value' => '',
                                             'option' => 'o',
                                             'compulsory' => false),
                 'format' => array('help' => 'output format (default, xml or html)',
                                             'get_arg_value' => 'xml',
                                             'option' => 'F',
                                             'compulsory' => false),
                 );
include('../libs/getopts.php');

// @todo : check $format for values
// @todo : check output for being a folder

// @question : should options be constants?

$INI['reader']['module'] = $INI['analyzer'];
$INI['reader']['file']   = $INI['file'];
$INI['reader']['output'] = $INI['output'];
$INI['reader']['format'] = $INI['format'];

// validations
if (empty($INI['reader']['format']) || !in_array($INI['reader']['format'],array('xml','html'))) {
    print "Output format is needed (option -F) : xml or html\n";
    print help();
    die();
}


// @todo : support later
//$summary = getOption($args, '-s', OPT_NO_VALUE, null);

include('../libs/database.php');
$DATABASE = new database();

// @todo support this later
//write_ini_file($INI, INI);

$query = 'SELECT * FROM <rapport_module> WHERE module='.$DATABASE->quote($INI['reader']['module']);
$res = $DATABASE->query($query);
$row = $res->fetch();
unset($res);

if (!$row) {
    print "No module with name '{$INI['reader']['module']}'. Aborting\n";
    die();
} elseif ($row['format'] == 'html') {
    // @attention : should also support _dot reports
    $query = 'SELECT * FROM <rapport> WHERE module='.$DATABASE->quote($INI['reader']['module']);
    if (!empty($INI['reader']['file'])) {
        $query .= ' AND fichier='.$DATABASE->quote($INI['reader']['file']);
    }
} elseif ($row['format'] == 'dot') {
    // @attention : should also support _dot reports
    $query = 'SELECT * FROM <rapport_dot> WHERE module='.$DATABASE->quote($INI['reader']['module']);
    if (!empty($INI['reader']['file'])) {
        $query .= ' AND fichier='.$DATABASE->quote($INI['reader']['file']);
    }
} else {
    print "Format '{$row['format']}' is not understood. Aborting\n";
    die();
}
$res = $DATABASE->query($query);

// @attention : should support -s for summaries. 

include('render/'.$INI['reader']['format'].'.php');
$class = "Render_".$INI['reader']['format'];

$view = new $class($INI['reader']['file']);

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

?>