<?php

class iffectations extends modules {
	protected	$description = 'Affectation dans un if';
	protected	$description_en = 'Affectation within a if';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();

	    $requete = <<<SQL
INSERT INTO <rapport>
   SELECT 0, T1.fichier, TC.code, T1.id, '{$this->name}'
FROM <tokens> T1
JOIN <tokens_tags> TT
  ON T1.id = TT.token_id 
JOIN <tokens> T2
  ON T1.fichier = T2.fichier AND TT.token_sub_id = T2.id
JOIN <tokens> T3
  ON T1.fichier = T3.fichier AND T3.droite BETWEEN T2.droite AND T2.gauche
JOIN <tokens>_cache TC
  ON TC.id = T3.id
  WHERE T1.type='ifthen' AND
        TT.type = 'condition' AND
        T3.type = 'affectation' AND
        T1.fichier = './tests/auditeur/scripts/iffectations.php';
SQL;
        $this->exec_query($requete);
	}
}

?>