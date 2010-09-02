<?php

class sql_queries extends noms {
	protected	$title = 'Requêtes SQL';
	protected	$description = 'Liste des requêtes SQL dans le texte';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
INSERT INTO <rapport>
    SELECT NULL, T1.fichier, T1.code AS code, T1.id, '{$this->name}', 0
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

        $this->exec_query($query);
        return true;
	}
}

?>