<?php

class inclusions2 extends modules {
	protected	$description = 'Liste des inclusions vers dot';
	protected	$description_en = 'Where files are included to dot';

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
SELECT distinct T1.fichier, T3.code,T1.fichier, '{$this->name}'
FROM <tokens> T1
    JOIN <tokens> T2
        ON T2.droite  = T1.droite + 1 AND
           T2.fichier = T1.fichier
    JOIN <tokens_cache> T3
        ON T3.id = T2.id
          AND T3.fichier = T2.fichier
	    WHERE T1.type='inclusion'
SQL;
        $res = $this->exec_query($requete);
        
// variables globales via $GLOBALS
       $requete = <<<SQL
INSERT INTO <rapport_dot> 
SELECT distinct T1.fichier, T2.code, T1.fichier, '{$this->name}'
    FROM <tokens> T1
    JOIN <tokens> T2
        ON T2.droite  = T1.droite + 1 AND
           T2.fichier = T1.fichier
	    WHERE T1.type='inclusion' AND
	          T2.type in ('literals','variable');
SQL;
        $res = $this->exec_query($requete);
	}
}

?>