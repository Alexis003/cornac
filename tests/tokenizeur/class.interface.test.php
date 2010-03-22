<?php
include_once('Analyseur_Framework_TestCase.php');

class Interface_Test extends Analyseur_Framework_TestCase
{
    /* 4 methodes */
    public function testInterface1()  { $this->generic_test('interface.1'); }
    public function testInterface2()  { $this->generic_test('interface.2'); }
    public function testInterface3()  { $this->generic_test('interface.3'); }
    public function testInterface4()  { $this->generic_test('interface.4'); }

}

?>