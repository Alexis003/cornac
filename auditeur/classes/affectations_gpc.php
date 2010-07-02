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

// variables, not whole arrays
        $requete = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, T1.fichier, TC.code, T1.id,'{$this->name}'  
FROM <tokens> T1  
    JOIN <tokens_tags> TT
ON T1.id = TT.token_id AND TT.type='right'
JOIN <tokens> T2
ON T2.fichier = T1.fichier AND TT.token_sub_id = T2.id
JOIN <tokens> T3
ON T3.fichier = T1.fichier AND 
T3.type='tableau' AND 
T3.droite between T2.droite AND T2.gauche 
JOIN <tokens_cache> TC
  ON TC.id = T3.id
WHERE T1.fichier like "%affectations_gpc%" and T1.type = 'affectation' AND
TC.code REGEXP BINARY '^\\\\\$(_GET|_POST|_COOKIE|_REQUEST|_ENV|_FILES|_SERVER|HTTP_GET_VARS|HTTP_POST_VARS)';
SQL;
        $this->exec_query($requete);

// full arrays,  not just variables
        $requete = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, T1.fichier, TC.code, T1.id,'{$this->name}'  
FROM <tokens> T1  
    JOIN <tokens_tags> TT
ON T1.id = TT.token_id AND TT.type='right'
JOIN <tokens> T2
ON T2.fichier = T1.fichier AND TT.token_sub_id = T2.id
JOIN <tokens> T3
ON T3.fichier = T1.fichier AND 
T3.type='variable' AND 
T3.droite between T2.droite AND T2.gauche 
LEFT JOIN <tokens> T4
ON T4.fichier = T1.fichier AND 
T4.droite=T3.droite -1 
JOIN <tokens_cache> TC
  ON TC.id = T3.id
WHERE T1.fichier like "%affectations_gpc%" and T1.type = 'affectation' AND
(T4.type IS NULL OR T4.type != 'tableau') AND 
TC.code REGEXP BINARY '^\\\\\$(_GET|_POST|_COOKIE|_REQUEST|_ENV|_FILES|_SERVER|HTTP_GET_VARS|HTTP_POST_VARS)';
SQL;
        $this->exec_query($requete);
    }
}

?>