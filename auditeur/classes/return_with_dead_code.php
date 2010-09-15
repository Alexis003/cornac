<?php 

class return_with_dead_code extends modules {
	protected	$title = 'Return avec code mort';
	protected	$description = 'Ceci est l\'analyseur return_with_dead_code par défaut. ';

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
INSERT INTO <rapport>
SELECT NULL, T1.fichier, CONCAT('ligne :',T1.ligne, ' : ', T1.fichier), T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens> T2
    ON T1.fichier = T2.fichier AND
       T1.droite BETWEEN T2.droite AND T2.gauche AND
       T2.type='_function'
WHERE T1.type='_return' AND
      T1.fichier LIKE "%analyseur.php%" AND
      T2.gauche != T1.gauche + 2 AND 
      T2.level = T1.level - 2;
SQL;
        $this->exec_query($query);
        
        return true;
	}
}

?>