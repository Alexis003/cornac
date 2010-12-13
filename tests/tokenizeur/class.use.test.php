<?php
include_once('Analyseur_Framework_TestCase.php');

class Use_Test extends Analyseur_Framework_TestCase
{
    /* 4 methodes */
    public function testUse1()  { $this->generic_test('use.1'); }
    public function testUse2()  { $this->generic_test('use.2'); }
    public function testUse3()  { $this->generic_test('use.3'); }
    public function testUse4()  { $this->generic_test('use.4'); }

}

?>