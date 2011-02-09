<?php



include_once('Auditeur_Framework_TestCase.php');

class Classes_Accessors_Test extends Auditeur_Framework_TestCase
{
    public function testClasses_Accessors()  {
        $this->expected = array( 'a->getB','a->setB',
                                 'd->getD','d->setD',
                                 'f->getE','f->setE');
        $this->unexpected = array('c->getC','c->setC');

        parent::generic_test();
    }
}
?>