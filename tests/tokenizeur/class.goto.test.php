<?php
include_once('Analyseur_Framework_TestCase.php');

class Goto_Test extends Analyseur_Framework_TestCase
{
    /* 4 methodes */
    public function testGoto1()  { $this->generic_test('goto.1'); }
    public function testGoto2()  { $this->generic_test('goto.2'); }
    public function testGoto3()  { $this->generic_test('goto.3'); }
    public function testGoto4()  { $this->generic_test('goto.4'); }

}

?>