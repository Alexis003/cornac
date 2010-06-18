<?php
include_once('Analyseur_Framework_TestCase.php');

class Commentaire_Test extends Analyseur_Framework_TestCase
{
    /* 5 methodes */
    public function testCommentaire1()  { $this->generic_test('commentaire.1'); }
    public function testCommentaire2()  { $this->generic_test('commentaire.2'); }
    public function testCommentaire3()  { $this->generic_test('commentaire.3'); }
    public function testCommentaire4()  { $this->generic_test('commentaire.4'); }
    public function testCommentaire5()  { $this->generic_test('commentaire.5'); }

}

?>