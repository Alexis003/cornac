<?php
include_once('Analyseur_Framework_TestCase.php');

class Global_Test extends Analyseur_Framework_TestCase
{
    /* 5 methodes */
    public function testGlobal1()  { $this->generic_test('global.1'); }
    public function testGlobal2()  { $this->generic_test('global.2'); }
    public function testGlobal3()  { $this->generic_test('global.3'); }
    public function testGlobal4()  { $this->generic_test('global.4'); }
    public function testGlobal5()  { $this->generic_test('global.5'); }

}

?>