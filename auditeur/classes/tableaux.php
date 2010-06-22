<?php

class tableaux extends modules {
	protected	$inverse = true;
	protected	$name = 'Classe sans nom';
	protected	$functions = array();
	protected	$not = false;

	protected	$description = 'Liste des tableaux et de leur usage';
	protected	$description_en = 'Variables being used';

	function __construct($mid) {
        parent::__construct($mid);
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        if ($this->not) {
            $not = ' not ';
        } else {
            $not = '';
        }
        
        $this->clean_rapport();

// cas simple : variable -> method
        $requete = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, T1.fichier, T2.code AS code, T1.id, '{$this->name}'
  from <tokens> T1
  join <tokens_cache> T2 
    on T1.id = T2.id
where 
 T1.type='tableau'
SQL;
        $this->exec_query($requete);


	}
	
}

?>