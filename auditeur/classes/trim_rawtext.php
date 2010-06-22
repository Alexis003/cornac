<?php

class trim_rawtext extends modules {
	protected	$description = 'Liste des fins de scripts type die ou exit';
	protected	$description_en = 'exit and die usage';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;

		$this->description = 'Utilisation de la fonction eval()';
		$this->description_en = 'eval() usage';
	}
	
	public function analyse() {
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport>
   SELECT NULL, T1.fichier, T1.code,  T1.id, 'parentheses'
   FROM <tokens> T1
   WHERE T1.type != 'codephp AND
         T1.droite = 0';
SQL;
    $this->exec_query($requete);

// extraction du dernier? 
//select * from 

	}
}

?>