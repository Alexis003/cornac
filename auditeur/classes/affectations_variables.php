<?php

class affectations_variables extends modules {
	protected	$description = 'Noms des variables affectées dans l\'application';
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
select 0, T1.fichier, T2.code, T1.id,'{$this->name}'  from <tokens> T1
JOIN <tokens> T2
    ON T1.fichier = T2.fichier AND T2.droite = T1.droite + 1
WHERE T1.type = 'affectation' 
SQL;
        $this->exec_query($requete);    
    }
}

?>