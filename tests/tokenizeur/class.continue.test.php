<?php
include_once('Analyseur_Framework_TestCase.php');

class Continue_Test extends Analyseur_Framework_TestCase
{
    /* 5 methodes */
    public function testContinue1()  { $this->generic_test('continue.1'); }
    public function testContinue2()  { $this->generic_test('continue.2'); }
    public function testContinue3()  { $this->generic_test('continue.3'); }
    public function testContinue4()  { $this->generic_test('continue.4'); }
    public function testContinue5()  { $this->generic_test('continue.5'); }

}

?>