<?php

class classes_undefined extends modules {
	protected	$description = 'Liste des classes qui ne sont pas déclarées';
	protected	$description_en = 'List of undefined classes';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}

	function dependsOn() {
	    return array('classes','_new');
	}
	
	public function analyse() {
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, TR1.fichier, TR1.element AS code, TR1.id, '{$this->name}'
    FROM <rapport>  TR1
    LEFT JOIN <rapport>  TR2 
    ON TR1.element = TR2.element AND TR2.module='classes' 
    WHERE TR1.module = '_new' AND TR2.element IS NULL
SQL;

// @todo excluding PHP classes ? 
//AND    TR1.element NOT IN ('$in');
        $this->exec_query($requete);
        return ;
	}
}

?>