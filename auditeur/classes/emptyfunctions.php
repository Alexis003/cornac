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

	    $requete = <<<SQL
INSERT INTO <rapport>
    SELECT 0, T1.fichier, group_concat(if(T2.droite = T1.droite + 1, T2.code, '') SEPARATOR '') , T2.id, '{$this->name}'
    FROM <tokens> T1 
    JOIN <tokens> T2 
        ON T2.droite > T1.droite AND
           T2.gauche < T1.gauche
    WHERE 
        T1.fichier='./tests.php' AND
        T1.type = '_function' AND 
        T2.fichier = './tests.php' 
        GROUP BY T1.id
        HAVING empty_bloc = 'vide'
SQL;
        $this->exec_query($requete);
	}
}

?>