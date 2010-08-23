<?php

class inclusions_path extends modules {
	protected	$description = 'Liste des chemins d\inclusions';
	protected	$description_en = 'List of inclusion paths';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport>
    SELECT NULL, T1.fichier, IFNULL(TC.code, T2.code) AS element, T1.id, '{$this->name}'
        FROM <tokens> T1
        JOIN <tokens> T2
            ON T1.fichier = T2.fichier AND
               T2.droite = T1.droite + 1
        LEFT JOIN phpmyadmin_cache TC
            ON TC.id = T2.id
    WHERE T1.type='inclusion'
SQL;
        $this->exec_query($requete);
	}
}
?>