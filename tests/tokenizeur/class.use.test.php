<?php
include_once('Analyseur_Framework_TestCase.php');

class Use_Test extends Analyseur_Framework_TestCase
{
    /* 2 methodes */
    public function testUse1()  { $this->generic_test('use.1'); }
    public function testUse2()  { $this->generic_test('use.2'); }

}

?>