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
include_once('Auditeur_Framework_TestCase.php');

class globals_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->expected = array('$global_de_scope','$g1','$g2','$g3','$GLOBALS[hors_x]','$GLOBALS[dans_x]');
        $this->inexpected = array('x','$x','$local_de_scope','$var_not_global','theclasse','$global_du_main_without_global','$GLOBALS');
        
        parent::generic_test();
    }
}

?>