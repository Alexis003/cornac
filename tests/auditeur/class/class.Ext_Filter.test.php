<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 - 2011                                            |
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
include_once('../../library/Cornac/Autoload.php');
spl_autoload_register('Cornac_Autoload::autoload');

class Ext_Filter_Test extends Cornac_Tests_Auditeur
{
    public function testfilter_functions()  {
        $this->expected = array( 
'filter_has_var',
'filter_id',
'filter_input',
'filter_input_array',
'filter_list',
'filter_var',
'filter_var_array',

);
        $this->unexpected = array(/*'',*/);

        parent::generic_test();
    }
}
?>