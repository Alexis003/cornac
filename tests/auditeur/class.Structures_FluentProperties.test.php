<?php 


include_once('Auditeur_Framework_TestCase.php');

class Structures_FluentProperties_Test extends Auditeur_Framework_TestCase
{
    public function testStructures_FluentProperties()  {
        $this->expected = array( '$that->is->a->fluent->property',
                                 '$this->is->a->another->fluent->property',
                                 '$this->is->yet->again->another->fluent->property',
                                  );
        $this->unexpected = array('$this->is_nothing_special',);

        parent::generic_test();
    }
}
?>