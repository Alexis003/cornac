<?php

class functions_unused extends modules {
    protected $not = false; 

	function __construct($mid) {
        parent::__construct($mid);
    	$this->name = __CLASS__;
	}
	
	function dependsOn() {
	    return array('functionscalls','deffunctions');
	}
	
	public function analyse() {
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, TR1.fichier, TR1.element AS code, TR1.id, '{$this->name}'
    FROM <rapport>  TR1
    LEFT JOIN <rapport>  TR2 
    ON TR1.element = TR2.element AND TR2.module='functionscalls' 
    WHERE TR1.module = 'deffunctions' AND TR2.module IS NULL
SQL;
        print $this->prepare_query($requete);
        $this->exec_query($requete);
	}
}

?>
