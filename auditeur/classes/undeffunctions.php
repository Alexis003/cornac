<?php

class undeffunctions extends modules {
	protected	$description = 'Liste des défintions de fonctions';
	protected	$description_en = 'List of functions definition';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport> 
   SELECT 0, T1.fichier, T2.code, T1.id, '{$this->name}'
   FROM <tokens> T1
    JOIN <tokens_tags> TT
        ON T1.id = TT.token_id  
    JOIN <tokens> T2 
        ON TT.token_sub_id = T2.id
    WHERE T1.type='_function'      AND 
          TT.type = 'name' AND
          T1.class = '';
SQL;
    
        $this->exec_query($requete);

	}
}

?>