<?php

class thrown extends modules {
	protected	$title = 'Exceptions thrown';
	protected	$description = 'Liste des emissions d\'exceptions';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

	    $query = <<<SQL
INSERT INTO <rapport>
SELECT NULL, T1.fichier, T2.code, T1.id, '{$this->name}' 
FROM <tokens> T1
JOIN <tokens>  T2
    ON T1.fichier = T2.fichier AND T1.droite + 2 = T2.droite
WHERE T1.type = '_throw'
SQL;
        $this->exec_query($query);
        
        return true;
	}
}

?>