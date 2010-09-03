<?php 

class defarray extends modules {
	protected	$title = 'Listes en tableaux ';
	protected	$description = 'Liste les tableaux qui contiennent des listes de valeurs';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array( );
	}
	
	public function analyse() {
        $this->clean_rapport();

// @todo of course, update this useless query. :)
	    $query = <<<SQL
INSERT INTO <rapport>
SELECT NULL, T2.fichier, CONCAT(SUM(IF(T3.type='token_traite',0,1)), ' elements'), T2.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens> T2
    ON T2.fichier = T1.fichier AND
       T2.droite = T1.gauche + 1
JOIN <tokens> T3
    ON T3.fichier = T1.fichier AND
       T3.droite BETWEEN T2.droite AND T2.gauche AND
       T3.level = T2.level + 1 
WHERE T1.code='array' AND 
       T2.type='arglist' AND
       T2.gauche - T2.droite > 1
GROUP BY T2.id;
SQL;
        $this->exec_query($query);
        
        return true;
	}
}

?>