<?php

class addElement_unaffected extends modules {
	protected	$title = 'addElement non affectés ';
	protected	$description = 'Recherche les utilisations de la méthode addElement qui ne sont pas affectés à une variable';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array('addElement');
	}
	
	public function analyse() {
        $this->clean_rapport();

	    $query = <<<SQL
SELECT NULL, T1.fichier, concat('ligne ',T1.ligne), T1.id, '{$this->name}', 0
FROM affility_rapport TR
JOIN affility T1
    ON T1.id = TR.token_id
LEFT JOIN affility T2
    ON T1.fichier = T2.fichier AND
       T1.droite BETWEEN T2.droite AND T2.gauche AND
       T2.type = 'affectation'
LEFT JOIN affility_tags TT
    ON TT.token_id=  T2.id AND
       TT.type = 'left'
LEFT JOIN affility T3
    ON T1.fichier = T3.fichier AND
       T3.id = TT.token_sub_id
WHERE TR.module='addElement' AND
      T3.id IS NULL
SQL;
        $this->exec_query_insert('rapport', $query);
        
        return true;
	}
}

?>