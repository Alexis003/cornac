#!/usr/bin/env php
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
include('libs/getopts.php');

$shell = './tokinit.php -I '.$INI['ini'].' -g '.$INI['templates'].' -K -r -d '.$INI['directory'];
if (isset($INI['directory'])) {
    shell_exec($shell.' -d '.$INI['directory']);
} elseif (isset($INI['file'])) {
    shell_exec($shell.' -f '.$INI['file']);
}

$ini = ' ';
if ($INI['log']) {
    $ini .= " -l ";
}

if ($INI['tokens']) {
    $ini .= " -t ";
}

$ini .= " -i ".$INI['limit'];
// @todo must remove the dependency to the database : this is silly
$ini .= ' -I '.($INI['ini'] ?: 'cornac');

print shell_exec('./tokclient.php '.$ini);

?>