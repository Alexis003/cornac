<?php

class sql_queries extends noms {
	protected	$description = 'Liste des requêtes SQL dans le texte';
	protected	$description_en = 'List of SQL queries';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport>
    SELECT 0, T1.fichier, T1.code AS code, T1.id, '{$this->name}'
    FROM <tokens> T1 
    WHERE T1.type = 'literals' and (
        (T1.code LIKE "%SELECT %" AND
         T1.code NOT LIKE "%<SELECT %" ) OR
        T1.code LIKE "%DELETE %" OR
        T1.code LIKE "%UPDATE %" OR
        T1.code LIKE "%INSERT %" OR
        T1.code LIKE "%CREATE TABLE%" OR
        T1.code LIKE "%JOIN%" OR
        T1.code LIKE "%ORDER BY%" OR
        T1.code LIKE "%JOIN%" OR
        T1.code LIKE "%WHERE%" OR
        T1.code LIKE "%HAVING %"
    )
SQL;

        $this->exec_query($requete);
        return;
	}
}

?>