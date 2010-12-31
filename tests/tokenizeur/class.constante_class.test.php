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

class Constante_class_Test extends Analyseur_Framework_TestCase
{
    /* 5 methodes */
    public function testConstante_class1()  { $this->generic_test('constante_class.1'); }
    public function testConstante_class2()  { $this->generic_test('constante_class.2'); }
    public function testConstante_class3()  { $this->generic_test('constante_class.3'); }
    public function testConstante_class4()  { $this->generic_test('constante_class.4'); }
    public function testConstante_class5()  { $this->generic_test('constante_class.5'); }

}

?>