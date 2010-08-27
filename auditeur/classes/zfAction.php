<?php

class zfAction extends modules {
	protected	$title = 'Actions Zend';
	protected	$description = 'Liste des fonctions méthodes de contrôleur pour le ZF (*Action)';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

	    $query = <<<SQL
INSERT INTO <rapport>
SELECT NULL, T1.fichier, T1.code, T1.id, '{$this->name}' 
    FROM <tokens> T1
    JOIN  <tokens_tags> TT
        ON TT.token_sub_id = T1.id
    WHERE 
        T1.code like "%Action"    
        AND TT.type = 'name'
    ;
SQL;
        $this->exec_query($query);
        
        return true;
	}
}

?>