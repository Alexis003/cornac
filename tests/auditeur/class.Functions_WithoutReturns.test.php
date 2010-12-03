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

class Functions_WithoutReturns_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->expected = array('function_without_return','fwr_x::method_without_return','fwr_x::static_method_without_return');
        $this->unexpected = array('function_with_return','method_with_return','static_method_with_return',
                                  '::function_with_return','::method_with_return','::static_method_with_return',
                                  'fwr_x::function_with_return','fwr_x::method_with_return','fwr_x::static_method_with_return'
        );
        
        parent::generic_test();
    }
}

?>