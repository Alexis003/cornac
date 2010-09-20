<?php

class functions_lines extends modules {
	protected	$title = 'Lignes';
	protected	$description = 'Nombres de lignes par fonction';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array();
	}
	
	public function analyse() {
        $this->clean_rapport();

// @todo of course, update this useless query. :)
	    $query = <<<SQL
SELECT NULL, T1.fichier, CONCAT( (T2.ligne - T1.ligne)), T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens> T2
    ON T1.fichier = T2.fichier AND
       T2.gauche = T1.gauche - 2
LEFT JOIN <tokens> T3
    ON T1.fichier = T3.fichier AND
       T3.droite BETWEEN T1.droite AND T1.gauche AND
       T3.type = 'literals' AND 
       T3.code = 'abstract'
WHERE T1.type='_function' 
SQL;
        $this->exec_query_insert('rapport', $query);
        
        return true;
	}
}

?>