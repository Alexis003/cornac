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

include('prepare/token.php');
include('prepare/token_traite.php');
include('prepare/instruction.php');
include('prepare/analyseur_regex.php');
include('prepare/variable.php');

$includes = glob('prepare/*.php');
foreach($includes as $include) {
    if ($include == "prepare/instruction.php") { continue; }
    if ($include == "prepare/token.php") { continue; }
    if ($include == "prepare/analyseur_regex.php") { continue; }
    if ($include == "prepare/variable.php") { continue; }
    if ($include == "prepare/token_traite.php") { continue; }
    if ($include == "prepare/analyseur.php") { continue; }
    if ($include == "prepare/commun.php") { continue; }
    include($include);
}
?>