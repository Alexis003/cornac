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

class Tableau_Test extends Analyseur_Framework_TestCase
{
    /* 12 methodes */
    public function testTableau1()  { $this->generic_test('tableau.1'); }
    public function testTableau2()  { $this->generic_test('tableau.2'); }
    public function testTableau3()  { $this->generic_test('tableau.3'); }
    public function testTableau4()  { $this->generic_test('tableau.4'); }
    public function testTableau5()  { $this->generic_test('tableau.5'); }
    public function testTableau6()  { $this->generic_test('tableau.6'); }
    public function testTableau7()  { $this->generic_test('tableau.7'); }
    public function testTableau8()  { $this->generic_test('tableau.8'); }
    public function testTableau9()  { $this->generic_test('tableau.9'); }
    public function testTableau10()  { $this->generic_test('tableau.10'); }
    public function testTableau11()  { $this->generic_test('tableau.11'); }
    public function testTableau12()  { $this->generic_test('tableau.12'); }

}

?>