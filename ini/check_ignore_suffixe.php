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
$list = ".gif,.jpg,.bak,.psd,.png,.htm,.xml,.js,.txt,.old,.gz,.db,.jpeg,.html,.swf,.scc,.rar,.zip,.ico,.sh,.patch,.sql,.dll,.ai,.afm,.jar,.docx,.dat,.mp3,.ttf,.table,.pdf,.z,.fla,.bmp,.cgi,.csv,.xsl,svg,.doc,.docx,.odt,.odp,.wsdl,.xsd,.dist,.markdown,.awk,.war,.md,.ott,.odg,.ts,.xmi,.dba,.ezt,.o,.cpp,.java,.manifest,.rng,.md5,.jsb,.in,.dia,.ctp,.sgml,.sample,.mxml,.dtd,.lin,.fre,.JPG";
$list = explode(',', $list);
$list2 = array_unique($list);

if(count($list2) != count($list)) { print "Il y a ".(count($list) - count($list2))." doublons dans la liste\n";}
asort($list2);

$list = join(',', $list2);

include('../libs/write_ini_file.php');

$files = glob('./*.ini');

foreach($files as $file) {
    $ini = parse_ini_file($file, true);
    if ($ini['tokenizeur']['ignore_suffixe'] != $list) {
        $ini['tokenizeur']['ignore_suffixe'] = $list;
        write_ini_file($ini, $file);
    
        print "$file : updated\n";
    } else {
        print "$file : ok\n";
    }
}

?>