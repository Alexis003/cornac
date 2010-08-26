<?php

class constantes_classes extends modules {
	protected	$title = 'Constantes de classe';
	protected	$description = 'Liste des constantes de classes définies';

    function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    }

	public function analyse() {
        $this->clean_rapport();

// @note cas simple : variable -> method
        $query = <<<SQL
INSERT INTO <rapport>
    SELECT NULL, T1.fichier, TC.code AS code, T1.id, '{$this->name}'
    FROM <tokens> T1 
    JOIN <tokens_cache> TC ON T1.id = TC.id
    WHERE T1.type = "constante_static"
SQL;
        $this->exec_query($query);
	    return true;
	}
	
}

?>