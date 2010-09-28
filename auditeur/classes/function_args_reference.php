<?php 

class function_args_reference extends modules {
	protected	$title = 'Fonctions avec références';
	protected	$description = 'Fonctions et méthodes avec références';

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
SELECT NULL, T1.fichier, CONCAT(T1.class,'::', T1.scope), T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens> T2
    ON T2.fichier = T1.fichier AND
       T2.level = T1.level + 1 AND
       T2.droite BETWEEN T1.droite AND T1.gauche AND
       T2.type = 'reference'
JOIN <tokens> T3
    ON T3.fichier = T1.fichier AND
       T3.droite = T2.droite + 1
WHERE T1.type = 'arglist'
SQL;
        $this->exec_query_insert('rapport', $query);

        return true;
	}
}

?>