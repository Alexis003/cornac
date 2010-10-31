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
 */include_once('Analyseur_Framework_TestCase.php');

class Global_Test extends Analyseur_Framework_TestCase
{
    /* 8 methodes */
    public function testGlobal1()  { $this->generic_test('global.1'); }
    public function testGlobal2()  { $this->generic_test('global.2'); }
    public function testGlobal3()  { $this->generic_test('global.3'); }
    public function testGlobal4()  { $this->generic_test('global.4'); }
    public function testGlobal5()  { $this->generic_test('global.5'); }
    public function testGlobal6()  { $this->generic_test('global.6'); }
    public function testGlobal7()  { $this->generic_test('global.7'); }
    public function testGlobal8()  { $this->generic_test('global.8'); }

}

?>