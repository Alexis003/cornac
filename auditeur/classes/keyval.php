<?php

class keyval extends modules {
	protected	$title = 'Variables de foreach';
	protected	$description = 'Liste des variables utilisées comme cle ou valeur dans un foreach';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array();
	}
	
	public function analyse() {
        $this->clean_rapport();

// @doc values
	    $query = <<<SQL
SELECT NULL, T1.fichier, T2.code, T1.id, '{$this->name}',0
FROM <tokens> T1
JOIN <tokens_tags> TT
    ON TT.token_id = T1.id AND
       TT.type='value'
JOIN <tokens> T2
    ON T1.fichier = T2.fichier AND
       TT.token_sub_id = T2.id AND
       T2.type = 'variable'
WHERE T1.type='_foreach';
SQL;
        $this->exec_query_insert('rapport',$query);

// @doc values as references
	    $query = <<<SQL
SELECT NULL, T1.fichier, T3.code, T1.id, '{$this->name}',0
FROM <tokens> T1
JOIN <tokens_tags> TT
    ON TT.token_id = T1.id AND
       TT.type='value'
JOIN <tokens> T2
    ON T1.fichier = T2.fichier AND
       TT.token_sub_id = T2.id
JOIN <tokens> T3
    ON T1.fichier = T3.fichier   AND
       T2.droite + 1 = T3.droite AND
       T3.type = 'variable'
WHERE T1.type='_foreach';
SQL;
        $this->exec_query_insert('rapport',$query);

// @doc keys
	    $query = <<<SQL
SELECT NULL, T1.fichier, T2.code, T1.id, '{$this->name}',0
FROM <tokens> T1
JOIN <tokens_tags> TT
    ON TT.token_id = T1.id AND
       TT.type='key'
JOIN <tokens> T2
    ON T1.fichier = T2.fichier AND
       TT.token_sub_id = T2.id
WHERE T1.type='_foreach';
SQL;
        $this->exec_query_insert('rapport',$query);
        
        return true;
	}
}

?>