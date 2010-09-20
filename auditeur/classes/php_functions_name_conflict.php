<?php 

class php_functions_name_conflict extends modules {
	protected	$title = 'Conflits de noms avec des fonctions PHP';
	protected	$description = 'Identifie des fonctions dont le nom est en conflit avec celles courantes de PHP';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array('deffunctions');
	}
	
	public function analyse() {
        $this->clean_rapport();

        $functions = modules::getPHPFunctions();
        $functions = join("','", $functions);

// @todo of course, update this useless query. :)
	    $query = <<<SQL
SELECT NULL, T1.fichier, T1.code, T1.id, '{$this->name}', 0
    FROM <tokens> T1
    WHERE   module = 'deffunctions' AND
            element IN ('$functions')
SQL;
        $this->exec_query_insert('rapport', $query);
        
        return true;
	}
}

?>