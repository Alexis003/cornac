<?php
include_once('Auditeur_Framework_TestCase.php');

class multidimarray_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array(
//'$x1[1]',
'$x2[2][3]',
'$x3[4][5][6]',
'$x4[$x[7][8]][9]',
'$x[7][8]',
//'$x10[1]',
'$x2[10][]',
'$x3[1][2]',
'$x3[][10]',
'$x3[1][2][3][4][5]',
'$x3[][]',
'$x6[10][20][30][40][50][60]',
);
        $this->inattendus = array();
        
        parent::generic_counted_test();
    }
}

?>