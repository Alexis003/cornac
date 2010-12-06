<?php
include_once('Analyseur_Framework_TestCase.php');

class Dowhile_Test extends Analyseur_Framework_TestCase
{
    /* 3 methodes */
    public function testDowhile1()  { $this->generic_test('dowhile.1'); }
    public function testDowhile2()  { $this->generic_test('dowhile.2'); }
    public function testDowhile3()  { $this->generic_test('dowhile.3'); }

}

?>