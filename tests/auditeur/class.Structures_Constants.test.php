<?php



include_once('Auditeur_Framework_TestCase.php');

class Structures_Constants_Test extends Auditeur_Framework_TestCase
{
    public function testStructures_Constants()  {
        $this->expected = array(     
    '(_constant VAL ? literals km : literals mois)',
    'a.__FILE__.b.true.c',
    '1 && 2',
    '(e == 3)',
    'strtolower(true)',
    'g(1, 2, 3, 4, 5)',
    'array(1, 2, 3)',
    'array(e => true)',
    ' (.m::n.)',
    '2 + 4',
    '_constant VAL ? literals km : literals mois',
 'km',
 'mois',
 'VAL',
 ')',
 ' (',
 'n',
 'm',
 ' (.m.)',
 'l',
 '(e => true)',
 'e => true',
 'true',
 'e',
 '3',
 '(1, 2, 3)',
 '2',
 '1',
 '(1, 2, 3, 4, 5)',
 '5',
 '4',
 '(false)',
 'false',
 '(true)',
 'e == 3',
 'c',
 'b',
 '__FILE__',
 'a',
 'm::n', 
 'array', 
 'g', 
 'strtolower',
 '==', 
 '&&', 
 '+'
);
        $this->unexpected = array(    '$e(false)',);

        parent::generic_test();
    }
}
?>