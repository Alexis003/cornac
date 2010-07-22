<?php

class tableaux extends modules {
	protected	$description = 'Liste des tableaux et de leur usage';
	protected	$description_en = 'Variables being used';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        if ($this->not) {
            $not = ' not ';
        } else {
            $not = '';
        }
        
        $this->clean_rapport();

// @note simple situation : variable -> index
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