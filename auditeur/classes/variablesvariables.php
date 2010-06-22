<?php

class variablesvariables extends modules {
	protected	$inverse = true;
	protected	$name = 'Classe sans nom';
	protected	$functions = array();

	protected	$description = 'Liste des variables variables';
	protected	$description_en = 'Variables variables being used';

    function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    }

	public function analyse() {
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport> 
   SELECT NULL, T1.fichier, TC.code AS code, T1.id, '{$this->name}'
   FROM <tokens> T1
    JOIN <tokens_cache> TC
        ON T1.id = TC.id  
    WHERE T1.type='variable'      AND 
          T1.gauche - T1.droite > 1
          ;
SQL;

        $this->exec_query($requete);
	}
	
}

?>