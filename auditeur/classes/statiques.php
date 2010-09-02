<?php

class statiques extends modules {
	protected	$title = 'Statiques';
	protected	$description = 'Liste des statiques (methodes, classes et propriétés)';

    function __construct($mid) {
        parent::__construct($mid);
    }

	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
INSERT INTO <rapport>
   SELECT NULL, T1.fichier, TC.code,  T1.id, '{$this->name}', 0
   FROM <tokens> T1
   JOIN <tokens_cache> TC
        ON TC.id = T1.id 
   WHERE type IN ('method_static','property_static','constante_static')
SQL;
        $this->exec_query($query);
        
        return true;
	}
	
}

?>