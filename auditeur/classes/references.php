<?php 

class references extends modules {
	protected	$title = 'References';
	protected	$description = 'Liste des références faites';

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
SELECT NULL, T1.fichier, TC.code, T1.id, '{$this->name}', 0
    FROM <tokens> T1
    JOIN <tokens> T2
        ON T1.fichier = T2.fichier AND
           T1.droite + 1 = T2.droite
    JOIN <tokens_cache> TC
        ON TC.id = T2.id
    WHERE T1.type = 'reference' 
SQL;
        $this->exec_query_insert('rapport', $query);
        
        return true;
	}
}

?>