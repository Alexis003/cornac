<?php
include_once('Analyseur_Framework_TestCase.php');

class Typehint_Test extends Analyseur_Framework_TestCase
{
    /* 5 methodes */
    public function testTypehint1()  { $this->generic_test('typehint.1'); }
    public function testTypehint2()  { $this->generic_test('typehint.2'); }
    public function testTypehint3()  { $this->generic_test('typehint.3'); }
    public function testTypehint4()  { $this->generic_test('typehint.4'); }
    public function testTypehint5()  { $this->generic_test('typehint.5'); }

}

?>