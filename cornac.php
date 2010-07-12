#!/usr/bin/php
<?php

include('libs/getopts.php');
$args = $argv;

if (get_arg($args, '-?')) { help(); }

// default values, stored in a INI file
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
        define('INI','ini/'.'tokenizeur.ini');
    }
    $INI = parse_ini_file(INI, true);
} else {
    define('INI',null);
    $INI = array('cornac' => array('destination' => ''));
}
$INI['cornac']['ini'] = $ini;
unset($ini);

$INI['cornac']['origin'] = get_arg_value($args, '-d', null);
if (is_null($INI['cornac']['origin'])) { 
    print "Origin folder is missing : option -d\n";
    help(); 
}

$INI['cornac']['destination'] = get_arg_value($args, '-o', @$INI['cornac']['destination']);
if (empty($INI['cornac']['destination'])) { 
    print "Destination folder is missing : option -o\n";
    help(); 
}

$INI['cornac']['storage'] = get_arg_value($args, '-s', @$INI['cornac']['storage'] ?: 'mysql' );
if (!in_array($INI['cornac']['storage'],array('mysql','sqlite'))) { 
    print "No storage provided : option -s\n";
    help(); 
}


// validations
if (!file_exists($INI['cornac']['origin'])) {
    print "Source folder '{$INI['cornac']['origin']}' doesn't exist\n";
    die();
}

if (!file_exists($INI['cornac']['destination'])) {
    print "Output folder '{$INI['cornac']['destination']}' doesn't exist\n";
    die();
}

if (realpath($INI['cornac']['origin']) == realpath($INI['cornac']['destination'])) {
    print "Please, don't use the same folder for source and destination\n";
    die();
}

// execution
print "
Folder : {$INI['cornac']['origin']} 
Output : {$INI['cornac']['destination']}\n";

if (!empty($INI['cornac']['ini'])) { $ini = " -I {$INI['cornac']['ini']} "; } else { $ini = ""; }

print shell_exec("./tokenizeur.php -r -d {$INI['cornac']['origin']} -g {$INI['cornac']['storage']},cache $ini "); // @todo : note the log 
                                                                                        // @sqlite as default ? 
print shell_exec("cd auditeur; ./auditeur.php $ini -o -d {$INI['cornac']['destination']}");
// @todo clean audits tables before. shell_exec("rm -rf /tmp/cornac; mkdir {$INI['destination']}");
print shell_exec("cd auditeur; ./reader.php $ini -F html -o {$INI['cornac']['destination']} ");

print "Done\n";


function help() {
    print <<<SHELL

Usage : ./cornac -d <source folder> -o <output folder>

Options : 
  -? : help
  -d : source folder
  -s : storage (mysql (default), sqlite)
  -o : output folder (path, 'web' (leave in database'))
  -I : ini config

SHELL;
    die();
}

?>