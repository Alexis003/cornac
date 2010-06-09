<?php

class tableaux_gpc extends modules {
	protected	$inverse = true;
	protected	$name = 'Classe sans nom';
	protected	$functions = array();
	protected	$not = false;

	protected	$description = 'Liste des tableaux et de leur usage';
	protected	$description_en = 'Variables being used';

	function __construct($mid) {
        parent::__construct($mid);
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        if ($this->not) {
            $not = ' not ';
        } else {
            $not = '';
        }
        
        $this->clean_rapport();

// cas simple : variable -> method
        $requete = <<<SQL
INSERT INTO <rapport>
    SELECT 0, T1.fichier, TC.code AS code, T1.id, '{$this->name}'
    FROM <tokens> T1 
    JOIN <tokens> T2 ON T1.droite + 1 = T2.droite AND T1.fichier = T2.fichier
    JOIN <tokens_cache> TC ON T1.id = TC.id
    WHERE T1.type="tableau" AND
          T2.code IN ('\$_GET','\$_SERVER','\$GLOBALS','\$_POST','\$_REQUEST','\$_ENV','\$_COOKIE','\$_SESSION')
SQL;
        $this->exec_query($requete);


	}
	
}

?>