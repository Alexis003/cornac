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
 
include('library/Cornac/Autoload.php');
spl_autoload_register('Cornac_Autoload::autoload');

// @synopsis : read options
$options = array('help' => array('help' => 'display this help',
                                 'option' => '?',
                                 'compulsory' => false),
                 'ini' => array('help' => 'configuration set or file',
                                 'get_arg_value' => null,
                                 'option' => 'I',
                                 'compulsory' => false),
                 'templates' => array('help' => 'output templates',
                                 'get_arg_value' => 'tree',
                                 'option' => 'g',
                                 'compulsory' => false),
                 'recursive' => array('help' => 'recursive mode',
                                      'option' => 'r',
                                      'compulsory' => false),
                 'file' => array('help' => 'file to work on',
                                 'get_arg_value' => null,
                                 'option' => 'f',
                                 'compulsory' => false),
                 'log' => array('help' => 'log activity',
                                          'option' => 'l',
                                          'compulsory' => false),
                 'tokens' => array('help' => 'only show tokens',
                                          'option' => 't',
                                          'compulsory' => false),
                 'directory' => array('help' => 'directory to work in',
                                      'get_arg_value' => null,
                                      'option' => 'd',
                                      'compulsory' => false),
                 'limit' => array('help' => 'limit the number of cycles (-1 for no limit)',
                                  'get_arg_value' => -1,
                                  'option' => 'i',
                                  'compulsory' => false),
                 );

$OPTIONS = new Cornac_Options();
$OPTIONS->setConfig($options);
$OPTIONS->init();

// @todo this should echo shell_exec values
// @todo try to remove the shell_exec, and use inclusion. This will speed up things
$shell = 'php tokinit.php -I '.$OPTIONS->ini.' -g '.$OPTIONS->templates.' -K ';
if (!empty($OPTIONS->directory)) {
    shell_exec($shell.' -r -d '.$OPTIONS->directory);
} elseif (!empty($OPTIONS->file)) {
    shell_exec($shell.' -f '.$OPTIONS->file);
}

$ini = ' ';
if ($OPTIONS->log) {
    $ini .= " -l ";
}

if ($OPTIONS->tokens) {
    $ini .= " -t ";
}

$ini .= " -i ".$OPTIONS->limit;
// @todo must remove the dependency to the database : this is silly
$ini .= ' -I '.($OPTIONS->ini ?: 'cornac');

print shell_exec('php tokclient.php '.$ini);

?>