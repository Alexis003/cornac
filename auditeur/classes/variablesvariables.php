<?php

class variablesvariables extends modules {
	protected	$title = 'variables variables';
	protected	$description = 'Liste des variables variables utilisées';

    function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    }

	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
INSERT INTO <rapport> 
   SELECT NULL, T1.fichier, TC.code AS code, T1.id, '{$this->name}'
   FROM <tokens> T1
    JOIN <tokens_cache> TC
        ON T1.id = TC.id  
    WHERE T1.type='variable'      AND 
          T1.gauche - T1.droite > 1
          ;
SQL;

        $this->exec_query($query);
	}
	
}

?>