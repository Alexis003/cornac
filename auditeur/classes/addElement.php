<?php 

class addElement extends modules {
	protected	$title = 'addElement utilisés';
	protected	$description = 'Recherche les utilisations de la méthode addElement';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array();
	}
	
	public function analyse() {
        $this->clean_rapport();

	    $query = <<<SQL
SELECT NULL, T1.fichier, T1.code, T1.id, 'addElement', 0
FROM <tokens> T1
JOIN <tokens> T2
    ON T1.fichier = T2.fichier AND
       T1.droite BETWEEN T2.droite AND T2.gauche AND
       T2.type = 'method' AND
       T2.level = T1.level - 2
WHERE T1.code = 'addElement'
SQL;
        $this->exec_query_insert('rapport', $query);
        
        return true;
	}
}

?>