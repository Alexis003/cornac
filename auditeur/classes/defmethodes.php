<?php

class defmethodes extends modules {
	protected	$title = 'Définitions de méthodes';
	protected	$description = 'Liste des défintions de methodes';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

        $concat = $this->concat("T1.class","'->'","T2.code");
        $query = <<<SQL
SELECT NULL, T1.fichier, $concat AS code, T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens_tags> TT
    ON T1.id = TT.token_id  
JOIN <tokens> T2 
    ON TT.token_sub_id = T2.id
WHERE T1.type='_function'      AND 
      TT.type = 'name' AND
      T1.class != ''
SQL;

        $this->exec_query_insert('rapport', $query);
        return true;
	}
}

?>