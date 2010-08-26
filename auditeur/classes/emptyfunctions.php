<?php

class emptyfunctions extends modules {
	protected	$title = 'Fonctions vides';
	protected	$description = 'Liste des fonctions avec un corps vide';

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
    LEFT JOIN <rapport> TR
        ON T1.fichier = TR.fichier AND
           T4.class = TR.element   AND
           TR.module='interfaces'
    WHERE 
        T1.type = '_function'      AND
        T2.type = 'block'          AND
        T3.gauche - T3.droite = 1  AND
        TR.id IS NULL
        ;
SQL;

        $this->exec_query($query);
	}
}

?>