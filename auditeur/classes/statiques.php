<?php

class statiques extends modules {
	protected	$description = 'Liste des statiques (methodes, classes et propriétés)';
	protected	$description_en = 'Static structures being used';

    function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    }

	public function analyse() {
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport>
   SELECT NULL, T1.fichier, TC.code,  T1.id, '{$this->name}'
   FROM <tokens> T1
   JOIN <tokens_cache> TC
        ON TC.id = T1.id 
   WHERE type IN ('method_static','property_static','constante_static')
SQL;
    $this->exec_query($requete);
	}
	
}

?>