<?php
include_once('Analyseur_Framework_TestCase.php');

class Shell_Test extends Analyseur_Framework_TestCase
{
    /* 3 methodes */
    public function testShell1()  { $this->generic_test('shell.1'); }
    public function testShell2()  { $this->generic_test('shell.2'); }
    public function testShell3()  { $this->generic_test('shell.3'); }

}

?>