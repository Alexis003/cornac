<?php

class proprietes_publiques extends modules {
	protected	$description = 'Appels d\'une fonction par une autre';
	protected	$description_en = 'Function call through the code';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    	$this->functions = array();
	}
	
	public function analyse() {
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport> 
   SELECT 0, T1.fichier, concat(T1.code, '->', T2.code), T1.id,  '{$this->name}'
FROM <tokens> T1 
JOIN <tokens_tags> TT 
  ON T1.id = TT.token_id AND TT.type = 'propriete' 
JOIN <tokens> T2 
ON T1.fichier = T2.fichier AND TT.token_sub_id = T2.id  
WHERE T1.type = 'property' AND T1.code != '\$this'
SQL;
        $this->exec_query($requete);

    // @todo supporter les méthodes / classes
    
    }
}

?>