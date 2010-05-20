<?php

class inclusions extends modules {
	protected	$description = 'Liste des inclusions';
	protected	$description_en = 'Where files are included';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport>
   SELECT 0, T1.fichier, T1.code, T1.id, '{$this->name}'
   FROM <tokens>  T1
   WHERE T1.type = 'inclusion';
SQL;

        $this->exec_query($requete);
	}
}

?>