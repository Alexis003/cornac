<?php

class variables_relations extends modules {
	protected	$title = 'Liens entre variables';
	protected	$description = 'Lien entre les variables';

	function __construct($mid) {
        parent::__construct($mid);
        
        $this->format = modules::FORMAT_DOT;
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();

// @todo : this should be done context by context. How can I do that? 
// @note I need another table for this        
        $query = <<<SQL
INSERT INTO <rapport_dot> 
  SELECT  T4.code, T2.code, CONCAT(T1.class,'::',T1.scope), '{$this->name}' 
FROM <tokens> T1
JOIN <tokens_tags> TT1
    ON T1.id = TT1.token_id AND TT1.type='left'
JOIN <tokens> T2
    ON T2.id = TT1.token_sub_id AND T2.type='variable' AND T1.fichier =T2.fichier
JOIN <tokens_tags> TT2
    ON T1.id = TT2.token_id AND TT2.type='right'
JOIN <tokens> T3
    ON T3.fichier = T1.fichier AND T3.id = TT2.token_sub_id
JOIN <tokens> T4
    ON T4.fichier = T1.fichier AND T4.droite BETWEEN T3.droite AND T3.gauche AND T4.type='variable'
WHERE T1.type = 'affectation'; 
SQL;
        $this->exec_query($query);

        return true;
	}
}

?>