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

class Structures_BlockOfCalls_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->expected = array('block4(0)
block4(1)
block4(2)
block4(3)',
'block5(0)
block5(1)
block5(2)
block5(3)
block5(4)',
'block3()
block3(1)
block3(2)'
                                );
        $this->unexpected = array();
        
        parent::generic_test();
    }
}

?>