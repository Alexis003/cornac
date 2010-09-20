<?php

class inclusions extends modules {
	protected	$title = 'Inclusions';
	protected	$description = 'Liste des inclusions (fonctions utilisées)';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
SELECT NULL, T1.fichier, T1.code, T1.id, '{$this->name}', 0
FROM <tokens> T1
WHERE T1.type = 'inclusion';
SQL;
        $this->exec_query_insert('rapport', $query);

        $query = <<<SQL
SELECT NULL, T1.fichier, T2.code, T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens_tags> TT 
    ON TT.token_id = T1.id
JOIN <tokens> T2
    ON TT.token_sub_id = T2.id AND
       T1.fichier = T2.fichier AND
       TT.type='fonction'      AND 
       T2.code='loadLibrary'
SQL;
        $this->exec_query_insert('rapport', $query);

        return true;
	}
}

?>