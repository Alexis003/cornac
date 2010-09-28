<?php 

class finals extends modules {
	protected	$title = 'Titre pour finals';
	protected	$description = 'Ceci est l\'analyseur finals par défaut. ';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array();
	}

	public function analyse() {
        $this->clean_rapport();

// @note spot final when in first place in a class
	    $query = <<<SQL
SELECT NULL, T1.fichier, T2.class, T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens> T2
    ON T2.fichier = T1.fichier AND
       T2.droite = T1.droite + 1 AND
       T2.code = 'final'
WHERE T1.type = '_class'
SQL;
        $this->exec_query_insert('rapport', $query);

// @note spot final when in first place in a method
	    $query = <<<SQL
SELECT NULL, T1.fichier, CONCAT(T2.class,'::',T2.scope), T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens> T2
    ON T2.fichier = T1.fichier AND
       T2.droite = T1.droite + 1 AND
       T2.code = 'final'
WHERE T1.type = '_function'
SQL;
        $this->exec_query_insert('rapport', $query);

// @note spot final when in second place
	    $query = <<<SQL
SELECT NULL, T1.fichier, CONCAT(T2.class,'::',T2.scope), T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens> T2
    ON T2.fichier = T1.fichier AND
       T2.droite = T1.droite + 3 AND
       T2.code = 'final'
WHERE T1.type = '_function'
SQL;
        $this->exec_query_insert('rapport', $query);

// @note spot final when in third place
	    $query = <<<SQL
SELECT NULL, T1.fichier, CONCAT(T2.class,'::',T2.scope), T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens> T2
    ON T2.fichier = T1.fichier AND
       T2.droite = T1.droite + 5 AND
       T2.code = 'final'
WHERE T1.type = '_function'
SQL;
        $this->exec_query_insert('rapport', $query);

        return true;
	}
}

?>