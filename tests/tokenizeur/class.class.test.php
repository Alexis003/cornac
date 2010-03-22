<?php
include_once('Analyseur_Framework_TestCase.php');

class Class_Test extends Analyseur_Framework_TestCase
{
    /* 3 methodes */
    public function testClass1()  { $this->generic_test('class.1'); }
    public function testClass2()  { $this->generic_test('class.2'); }
    public function testClass3()  { $this->generic_test('class.3'); }

}

?>