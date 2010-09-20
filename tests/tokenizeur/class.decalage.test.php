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

class Decalage_Test extends Analyseur_Framework_TestCase
{
    /* 10 methodes */
    public function testDecalage1()  { $this->generic_test('decalage.1'); }
    public function testDecalage2()  { $this->generic_test('decalage.2'); }
    public function testDecalage3()  { $this->generic_test('decalage.3'); }
    public function testDecalage4()  { $this->generic_test('decalage.4'); }
    public function testDecalage5()  { $this->generic_test('decalage.5'); }
    public function testDecalage6()  { $this->generic_test('decalage.6'); }
    public function testDecalage7()  { $this->generic_test('decalage.7'); }
    public function testDecalage8()  { $this->generic_test('decalage.8'); }
    public function testDecalage9()  { $this->generic_test('decalage.9'); }
    public function testDecalage10()  { $this->generic_test('decalage.10'); }

}

?>