<?php

class tableaux_gpc extends modules {
	protected	$title = 'Tableaux et leur index';
	protected	$description = 'Liste des tableaux et de leur usage, avec leurs index';

	function __construct($mid) {
        parent::__construct($mid);
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();

// @note : simple situation : variable -> method
        $query = <<<SQL
INSERT INTO <rapport>
    SELECT NULL, T1.fichier, TC.code AS code, T1.id, '{$this->name}'
    FROM <tokens> T1 
    JOIN <tokens> T2 ON T1.droite + 1 = T2.droite AND T1.fichier = T2.fichier
    JOIN <tokens_cache> TC ON T1.id = TC.id
    WHERE T1.type="tableau" AND
          T2.code IN ('\$_GET','\$_SERVER','\$GLOBALS','\$_POST','\$_REQUEST','\$_ENV','\$_COOKIE','\$_SESSION')
SQL;
        $this->exec_query($query);

        return true;
	}
}

?>