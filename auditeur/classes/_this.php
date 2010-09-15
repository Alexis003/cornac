<?php 

class _this extends modules {
	protected	$title = 'Utilisation indue de $this';
	protected	$description = 'Recherche les utilisations de $this hors d\'une classe.';

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
INSERT INTO <rapport>
SELECT NULL, T1.fichier, T1.code, T1.id, '{$this->name}', 0
    FROM tu T1
    WHERE code = '\$this' AND 
          class = ''      AND
          type = 'variable'
SQL;
        $this->exec_query($query);
        
        return true;
	}
}

?>