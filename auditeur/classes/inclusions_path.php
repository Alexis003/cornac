<?php

class inclusions_path extends modules {
	protected	$title = 'Chemin d\'inclusion';
	protected	$description = 'Liste des chemins d\inclusions utilisés (hors __autoload)';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
INSERT INTO <rapport>
    SELECT NULL, T1.fichier, IFNULL(TC.code, T2.code) AS element, T1.id, '{$this->name}', 0
        FROM <tokens> T1
        JOIN <tokens> T2
            ON T1.fichier = T2.fichier AND
               T2.droite = T1.droite + 1
        LEFT JOIN <tokens_cache> TC
            ON TC.id = T2.id
    WHERE T1.type='inclusion'
SQL;
        $this->exec_query($query);
        
        return true;
	}
}
?>