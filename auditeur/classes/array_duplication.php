<?php

class array_duplication extends modules {
	protected	$description = 'Foreach duplicant un tableau';
	protected	$description_en = 'Foreach making a copy of an array';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport>
   SELECT NULL, T1.fichier, T2.code, T1.id, '{$this->name}'
   FROM <tokens>  T1
   JOIN <tokens> T2
       ON T1.droite + 1 = T2.droite AND 
          T1.fichier = T2.fichier
   WHERE T1.type = '_new';
SQL;

        $this->exec_query($requete);

	}
}
?>