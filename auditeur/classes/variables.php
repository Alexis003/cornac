<?php

class variables extends modules {
	protected	$description = 'Liste des variables et de leur usage';
	protected	$description_en = 'Variables being used';

    function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    }

	public function analyse() {
        $requete = <<<SQL
INSERT INTO <rapport>
    SELECT NULL, T1.fichier, T1.code AS code, T1.id, '{$this->name}'
    FROM <tokens> T1 
    WHERE T1.type = 'variable' AND T1.code != '$'
SQL;
	$this->exec_query($requete);
	
	}
}

?>