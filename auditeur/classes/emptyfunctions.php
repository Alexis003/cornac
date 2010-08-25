<?php

class emptyfunctions extends modules {
	protected	$description = 'Liste des fonctions vides';
	protected	$description_en = 'List of empty functions';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();

	    $query = <<<SQL
INSERT INTO <rapport>
    SELECT NULL, T1.fichier, T4.code, T1.id, '{$this->name}'
    FROM <tokens> T1 
    JOIN <tokens_tags> T2
        ON T1.id = T2.token_id
    JOIN <tokens> T3
    ON T3.id = T2.token_sub_id
    JOIN <tokens_tags> T5
        ON T1.id = T5.token_id AND T5.type = 'name'
    JOIN <tokens> T4
    ON T1.fichier = T4.fichier AND
       T4.id = T5.token_sub_id
    WHERE 
        T1.type = '_function' AND
        T2.type = 'block' AND
        T3.gauche - T3.droite = 1
        ;
SQL;

    $this->exec_query($query);
	}
}

?>