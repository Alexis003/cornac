<?php
include_once('Auditeur_Framework_TestCase.php');

class Constantes_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'constantes';
        $this->attendus = array(
        'true','FALSE','__METHOD__','__FILE__','CONSTANTE_USER',
        'CONSTANTE_FONCTION1','CONSTANTE_FONCTION2','CONSTANTE_FONCTION3',
        'CONSTANTE_ARRAY1','CONSTANTE_ARRAY2','CONSTANTE_ARRAY3',
        'CONSTANTE_TABLEAU',
        );
        $this->inattendus = array('fonction','array');
        
        parent::generic_test();
    }
}

?>

