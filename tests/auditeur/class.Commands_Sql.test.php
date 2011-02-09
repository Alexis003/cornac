<?php



include_once('Auditeur_Framework_TestCase.php');

class Commands_Sql_Test extends Auditeur_Framework_TestCase
{
    public function testCommands_Sql()  {
        $this->expected = array( 'SELECT * FROM table', 'Where X =1 ');
        $this->unexpected = array('other string non SQL',);

        parent::generic_test();
    }
}
?>