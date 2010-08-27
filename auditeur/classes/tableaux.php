<?php

class tableaux extends modules {
	protected	$title = 'Tableaux';
	protected	$description = 'Liste des tableaux (array) utilisés';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

// @note simple situation : variable -> index
        $query = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, T1.fichier, T2.code AS code, T1.id, '{$this->name}'
  FROM <tokens> T1
  JOIN <tokens_cache> T2 
    ON T1.id = T2.id
WHERE 
 T1.type='tableau'
SQL;
        $this->exec_query($query);

        return true;
	}
}

?>