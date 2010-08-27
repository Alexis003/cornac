<?php

class affectations_literals extends modules {
	protected	$title = 'Assignations de litéraux';
	protected	$description = 'Affectations de valeurs literales';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();

// @note affectations that have no variables on the right side (properties, references, list(), noscream...)
        $query = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, T1.fichier, TC.code, T1.id,  '{$this->name}'  
FROM <tokens> T1
JOIN <tokens_tags> TT1
ON T1.id = TT1.token_id AND TT1.type='right'
JOIN <tokens> T2
    ON T1.fichier = T2.fichier AND T2.id = TT1.token_sub_id
JOIN <tokens> T3
    ON T1.fichier = T3.fichier AND T3.droite BETWEEN T2.droite AND T2.gauche 
JOIN <tokens_cache> TC
    ON TC.id = T1.id
WHERE T1.type = 'affectation' 
GROUP BY T1.id
HAVING SUM(IF(T3.type = 'variable', 1,0)) = 0
SQL;
        $this->exec_query($query);    
    }
}

?>