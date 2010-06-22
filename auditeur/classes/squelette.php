<?php

class zfAction extends modules {
	protected	$description = 'Liste des fonctions méthodes de contrôleur pour le ZF (*Action)';
	protected	$description_en = 'List of action method from controlers in ZF ';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();

	    $requete = <<<SQL
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
    $this->exec_query($requete);
	}
}

?>