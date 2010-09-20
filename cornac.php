#!/usr/bin/php
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

include('libs/getopts.php');
include('libs/write_ini_file.php');
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
        $INI = parse_ini_file('ini/'.'cornac.ini', true);
        write_ini_file($INI,'ini/'.$ini.'.ini'); 
        define('INI','ini/'.$ini.'.ini');
    }
    $INI = parse_ini_file(INI, true);
} else {
    define('INI',null);
    $INI = array('cornac' => array('destination' => ''));
}
$INI['cornac']['ini'] = $ini;
$INI['cornac']['prefix'] = $ini;
unset($ini);

$INI['cornac']['origin'] = get_arg_value($args, '-d', $INI['cornac']['origin']);
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

if (!file_exists($INI['cornac']['destination'])) { 
    if (!mkdir($INI['cornac']['destination'])) {
        print "Output directory doesn't exist '{$INI['cornac']['destination']}' : update ".INI."\n";
        help(); 
    }
}

if (!is_dir($INI['cornac']['destination'])) { 
    print "Output path '{$INI['cornac']['destination']}' isn't a directory : update ".INI."\n";
    help(); 
}

if (!is_writable($INI['cornac']['destination'])) { 
    print "Output path '{$INI['cornac']['destination']}' isn't writable : update ".INI."\n";
    help(); 
}
$INI['reader']['output'] = $INI['cornac']['destination'];

// @notes validations
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

if ($INI['cornac']['storage'] == 'mysql') {
    $INI['mysql']['active'] = 1;
    $INI['sqlite']['active'] = 0;

    if (get_arg($args, '-K')) { 
        $database = new pdo($INI['mysql']['dsn'],$INI['mysql']['username'], $INI['mysql']['password']);
        $table = $INI['cornac']['prefix'] ?: 'tokens'; 
        // @todo add some more verifications (existence, number actually destroyed..)
        $database->query("DROP TABLE {$table}, {$table}_cache, {$table}_rapport, {$table}_rapport_dot, {$table}_rapport_module, {$table}_tags");
        print "tables {$table}_* erased\n";
    }
} elseif ($INI['cornac']['storage'] == 'sqlite') {
    $INI['mysql']['active'] = 0;
    $INI['sqlite']['active'] = 1;
} else {
    print "Please, storage should be mysql or sqlite\n";
    die();
}

write_ini_file($INI, INI);
// execution
print "
Folder : {$INI['cornac']['origin']} 
Output : {$INI['cornac']['destination']}\n";

if (!empty($INI['cornac']['ini'])) { $ini = " -I {$INI['cornac']['ini']} "; } else { $ini = ""; }

print "Tokenizeur\n";
shell_exec("./tokenizeur.php -r -d {$INI['cornac']['origin']} -g {$INI['cornac']['storage']},cache $ini "); // @todo : note the log 
                                                                                                            // @sqlite as default ? 
print "Auditeur\n";
shell_exec("cd auditeur; ./auditeur.php $ini -o -d {$INI['cornac']['destination']}");
// @todo clean audits tables before. 

print "Export\n";
shell_exec("cd auditeur; ./reader.php $ini -F html -o {$INI['cornac']['destination']} ");

print "Done\n";

?>