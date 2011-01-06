<?php
include_once('Analyseur_Framework_TestCase.php');

class Namespace_Test extends Analyseur_Framework_TestCase
{
    /* 4 methodes */
    public function testNamespace1()  { $this->generic_test('namespace.1'); }
    public function testNamespace2()  { $this->generic_test('namespace.2'); }
    public function testNamespace3()  { $this->generic_test('namespace.3'); }
    public function testNamespace4()  { $this->generic_test('namespace.4'); }

}

?>