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

class properties_defined_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array('x->$a','x->$b','x->$c','x->$d','x->$e','x->$f','x->$g','x->$h','x->$i','x->$j','x->$k','x->$l','x->$m','x->$n','x->$o','x->$p','x->$q','x->$r','x->$s','x->$es','x->$fs','x->$gs','x->$hs','x->$is','x->$se','x->$sf','x->$sg','x->$sh','x->$si');
        $this->inattendus = array('$arg','$local','x');
        
        parent::generic_test();
    }
}

?>