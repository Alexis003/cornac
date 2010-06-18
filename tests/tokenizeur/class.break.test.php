<?php
include_once('Analyseur_Framework_TestCase.php');

class Break_Test extends Analyseur_Framework_TestCase
{
    /* 3 methodes */
    public function testBreak1()  { $this->generic_test('break.1'); }
    public function testBreak2()  { $this->generic_test('break.2'); }
    public function testBreak3()  { $this->generic_test('break.3'); }

}

?>