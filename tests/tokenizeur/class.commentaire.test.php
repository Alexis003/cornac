<?php

include_once('Analyseur_Framework_TestCase.php');

class Commentaire_Test extends Analyseur_Framework_TestCase
{
    protected function setUp()
    {

    }
 
    protected function tearDown()
    {

    }

    public function testCommentaire1()  { $this->generic_test('commentaire.1'); }
    public function testCommentaire2()  { $this->generic_test('commentaire.2'); }
    public function testCommentaire3()  { $this->generic_test('commentaire.3'); }
    public function testCommentaire4()  { $this->generic_test('commentaire.4'); }
/*    public function testCommentaire5()  { $this->generic_test('commentaire.5'); }
    public function testCommentaire6()  { $this->generic_test('commentaire.6'); }
    public function testCommentaire7()  { $this->generic_test('commentaire.7'); }
    public function testCommentaire8()  { $this->generic_test('commentaire.8'); }*/
}
?>