<?php

class functions_unused extends modules {
    protected $title = 'Fonctions non utilisées'; 
    protected $description = 'Liste des fonctions non utilisées'; 

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	function dependsOn() {
	    return array('functionscalls','deffunctions');
	}
	
	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, TR1.fichier, TR1.element AS code, TR1.id, '{$this->name}', 0
    FROM <rapport>  TR1
    LEFT JOIN <rapport>  TR2 
    ON TR1.element = TR2.element AND TR2.module='functionscalls' 
    WHERE TR1.module = 'deffunctions' AND 
          TR2.module IS NULL AND
          TR1.element NOT IN ('__autoload')
SQL;
        $this->exec_query($query);
        return true;
	}
}

?>