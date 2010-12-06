<?php
include_once('Analyseur_Framework_TestCase.php');

class Namespace_Test extends Analyseur_Framework_TestCase
{
    /* 2 methodes */
    public function testNamespace1()  { $this->generic_test('namespace.1'); }
    public function testNamespace2()  { $this->generic_test('namespace.2'); }

}

?>