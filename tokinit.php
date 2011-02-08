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
// @synopsis : inclusions
include('library/Cornac/Autoload.php');
spl_autoload_register('Cornac_Autoload::autoload');

include("prepare/analyseur.php");

// @synopsis : configuration
ini_set('memory_limit',234217728);

// @synopsis : read options
$options = array('help' => array('help' => 'display this help',
                                 'option' => '?',
                                 'compulsory' => false),
                 'ini' => array('help' => 'configuration set or file',
                                 'get_arg_value' => null,
                                 'option' => 'I',
                                 'compulsory' => true),
                 'templates' => array('help' => 'output templates',
                                 'get_arg_value' => 'tree',
                                 'option' => 'g',
                                 'compulsory' => false),
                 'clean' => array('help' => 'clean tasks',
                                 'option' => 'K',
                                 'compulsory' => false),
                 'recursive' => array('help' => 'recursive mode',
                                      'option' => 'r',
                                      'compulsory' => false),
                 'file' => array('help' => 'file to work on',
                                 'get_arg_value' => null,
                                 'option' => 'f',
                                 'compulsory' => false),
                 'directory' => array('help' => 'directory to work in',
                                      'get_arg_value' => null,
                                      'option' => 'd',
                                      'compulsory' => false),
                 );
include('libs/getopts.php');

// @todo make the autoload
$log = new Cornac_Log('tokinit');
Cornac_Log::getInstance('tokinit')->log("Token Init");

// @synopsis : next
global $FIN;
// Collecting tokens
$FIN['debut'] = microtime(true);

// @todo : exporter les informations d'options dans une inclusion

// @doc default values, stored in a INI file

$templates = explode(',', $INI['templates']);
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

$DATABASE = new Cornac_Database();

$query = <<<SQL
CREATE TABLE IF NOT EXISTS `<tasks>` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `task` enum('tokenize','auditeur') NOT NULL,
  `target` varchar(255) NOT NULL,
  `template` varchar(100) NOT NULL,
  `date_update` datetime NOT NULL,
  `completed` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fichiers` (`task`,`target`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC
SQL;
$DATABASE->query($query);

if (isset($INI['clean'])) {
    $query = "DELETE FROM <tasks>";
    $DATABASE->query($query);
}

// @synopsis core of the code
if (isset($INI['directory'])) {
    if (substr($INI['directory'], -1) == '/') {
        $directory = substr($INI['directory'], 0, -1);
    } else {
        $directory = $INI['directory'];
    }

    if (!file_exists($directory)) {
        print "Couldn't find directory '$directory'\n Aborting\n";
        die();
    }

    print "Preparing work on directory '{$directory}'\n";

    $spl = new Cornac_Dir_RecursiveDirectoryIterator($directory);
    $files = $spl->list_files();

    foreach($files as $file) {
        $code = file_get_contents($file);
        if (strpos($code, '<?') === false) { continue; }

        $query = "INSERT IGNORE INTO <tasks> VALUES (NULL, 'tokenize', ".$DATABASE->quote($file).", ".$DATABASE->quote(GABARIT).",NOW(), 0)";
        $DATABASE->query($query);
    }
} elseif (isset($INI['file'])) {
    $file = $INI['file'];
    if (!is_file($file)) {
        print "'$file' is a directory. Use -d option. Aborting\n";
        die();
    }
    print "Working on file '{$file}'\n";

    $query = "INSERT IGNORE INTO <tasks> VALUES (NULL, 'tokenize', ".$DATABASE->quote($file).", ".$DATABASE->quote(GABARIT).", NOW(), 0)";
    $DATABASE->query($query);
} else {
    print "No files to work on\n";
    help();
    die();
}

print "Done\n";

?>