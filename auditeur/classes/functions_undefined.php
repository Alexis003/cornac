<?php

class functions_undefined extends modules {
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

	    $total = modules::getPHPFunctions();
	    $in = join("', '", $total);

        $requete = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, TR1.fichier, TR1.element AS code, TR1.id, '{$this->name}'
    FROM <rapport>  TR1
    LEFT JOIN <rapport>  TR2 
    ON TR1.element = TR2.element AND TR2.module='deffunctions' 
    WHERE TR1.module = 'functionscalls' AND TR2.element IS NULL AND
    TR1.element NOT IN ('$in');
SQL;

        $this->exec_query($requete);


	}
}

?>
