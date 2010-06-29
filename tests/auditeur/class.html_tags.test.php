<?php
include_once('Auditeur_Framework_TestCase.php');

class html_tags_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'html_tags';
        $this->attendus = array('<br />','</div >','<b>', '<hr> < BR> <P>');
        $this->inattendus = array('<table id=\"$id\">',);
        
        parent::generic_test();
    }
}
?>