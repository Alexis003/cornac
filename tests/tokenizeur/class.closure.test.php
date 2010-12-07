<?php
include_once('Analyseur_Framework_TestCase.php');

class Closure_Test extends Analyseur_Framework_TestCase
{
    /* 3 methodes */
    public function testClosure1()  { $this->generic_test('closure.1'); }
    public function testClosure2()  { $this->generic_test('closure.2'); }
    public function testClosure3()  { $this->generic_test('closure.3'); }

}

?>