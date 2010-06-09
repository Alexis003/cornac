<?php

class appelsfonctions extends modules {
	protected	$description = 'Appels d\'une fonction par une autre';
	protected	$description_en = 'Function call through the code';

	function __construct($mid) {
        parent::__construct($mid);
        
        $this->format = modules::FORMAT_DOT;
    	$this->name = __CLASS__;
    	$this->functions = array();
	}
	
	public function analyse() {
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport_dot> 
SELECT distinct T5.code, T3.code,T1.fichier, '{$this->name}'
FROM <tokens> T1
JOIN <tokens> T2
ON T1.fichier = T2.fichier AND
   T2.droite BETWEEN T1.droite AND T1.gauche AND
   T2.type = 'functioncall'
JOIN <tokens> T3
ON T1.fichier = T3.fichier AND
   T3.droite = T2.droite + 1
JOIN <tokens_tags> T4
ON T1.id = T4.token_id AND
   T4.type='name'
JOIN <tokens> T5
ON T1.fichier = T5.fichier AND
   T4.token_sub_id = T5.id
WHERE T1.type='_function'  AND
      T2.type='functioncall';
SQL;
        $this->exec_query($requete);

    // @todo supporter les méthodes / classes
    
    }
}

?>