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

class Clevaleur_Test extends Analyseur_Framework_TestCase
{
    /* 8 methodes */
    public function testClevaleur1()  { $this->generic_test('clevaleur.1'); }
    public function testClevaleur2()  { $this->generic_test('clevaleur.2'); }
    public function testClevaleur3()  { $this->generic_test('clevaleur.3'); }
    public function testClevaleur4()  { $this->generic_test('clevaleur.4'); }
    public function testClevaleur5()  { $this->generic_test('clevaleur.5'); }
    public function testClevaleur6()  { $this->generic_test('clevaleur.6'); }
    public function testClevaleur7()  { $this->generic_test('clevaleur.7'); }
    public function testClevaleur8()  { $this->generic_test('clevaleur.8'); }

}

?>