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
include_once('Analyseur_Framework_TestCase.php');

class Typehint_Test extends Analyseur_Framework_TestCase
{
    /* 6 methodes */
    public function testTypehint1()  { $this->generic_test('typehint.1'); }
    public function testTypehint2()  { $this->generic_test('typehint.2'); }
    public function testTypehint3()  { $this->generic_test('typehint.3'); }
    public function testTypehint4()  { $this->generic_test('typehint.4'); }
    public function testTypehint5()  { $this->generic_test('typehint.5'); }
    public function testTypehint6()  { $this->generic_test('typehint.6'); }

}

?>