<?php

class php_constant_name_conflict extends modules {
	protected	$title = 'Conflits de noms avec des constantes PHP';
	protected	$description = 'Identifie des constantes dont le nom est en conflit avec celles courantes de PHP';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array('defconstantes');
	}
	
	public function analyse() {
        $this->clean_rapport();

        $constants = modules::getPHPConstants();
        $constants = join("','", $constants);

// @todo of course, update this useless query. :)
	    $query = <<<SQL
SELECT NULL, T1.fichier, T1.element, T1.id, '{$this->name}', 0
    FROM <rapport> T1
    WHERE   T1.module = 'defconstantes' AND
            T1.element IN ('$constants')
SQL;
        $this->exec_query_insert('rapport', $query);
        
        return true;
	}
}

?>