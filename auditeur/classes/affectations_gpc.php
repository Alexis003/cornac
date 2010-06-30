<?php

class affectations_gpc extends modules {
	protected	$description = 'Affectations des variables GPC (pb de sécurité)';
	protected	$description_en = 'Assigning GPC vars (security to check)';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, T1.fichier, T3.code, T1.id,'{$this->name}'  
FROM <tokens> T1  
    JOIN <tokens_tags> TT
ON T1.id = TT.token_id AND TT.type='right'
JOIN <tokens> T2
ON T2.fichier = T1.fichier AND TT.token_sub_id = T2.id
JOIN <tokens> T3
ON T3.fichier = T1.fichier AND 
   T3.droite between T2.droite AND
   T2.gauche AND T3.type='variable' AND
   T3.code IN ('\$_GET','\$_POST','\$_REQUEST', '\$_SERVER','\$HTTP_RAW_POST_VARS')
WHERE T1.type='affectation';
SQL;
        $this->exec_query($requete);
    }
}

?>