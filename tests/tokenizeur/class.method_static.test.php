<?php
include_once('Analyseur_Framework_TestCase.php');

class Method_static_Test extends Analyseur_Framework_TestCase
{
    /* 7 methodes */
    public function testMethod_static1()  { $this->generic_test('method_static.1'); }
    public function testMethod_static2()  { $this->generic_test('method_static.2'); }
    public function testMethod_static3()  { $this->generic_test('method_static.3'); }
    public function testMethod_static4()  { $this->generic_test('method_static.4'); }
    public function testMethod_static5()  { $this->generic_test('method_static.5'); }
    public function testMethod_static6()  { $this->generic_test('method_static.6'); }
    public function testMethod_static7()  { $this->generic_test('method_static.7'); }

}

?>