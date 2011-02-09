<?php



include_once('Auditeur_Framework_TestCase.php');

class Commands_SqlConcatenation_Test extends Auditeur_Framework_TestCase
{
    public function testCommands_SqlConcatenation()  {
        $this->expected = array( 
'SELECT * FROM .$table',
'DELETE FROM .$table',
'UPDATE .$table. SET x = 1',
);
        $this->unexpected = array('Where X =1  /* non concatenation */',
                                  'other string non SQL');

        parent::generic_test();
    }
}
?>